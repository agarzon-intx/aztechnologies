#!/usr/bin/env python3
"""Production SFTP upload/delete (paramiko). Used by deploy-production-files.sh."""
from __future__ import annotations

import argparse
import os
import posixpath
import sys


def load_env(repo: str) -> dict[str, str]:
    path = os.path.join(repo, ".local", "sftp-development.env")
    cfg: dict[str, str] = {}
    with open(path, encoding="utf-8", errors="replace") as f:
        for line in f:
            line = line.strip().strip("\r")
            if not line or line.startswith("#") or "=" not in line:
                continue
            k, v = line.split("=", 1)
            cfg[k.strip()] = v.strip()
    for key in ("SFTP_HOST", "SFTP_USER", "SFTP_PASSWORD", "SFTP_PRODUCTION_BASE"):
        if not cfg.get(key):
            raise SystemExit(f"Missing {key} in {path}")
    return cfg


def remote_root(production_base: str) -> str:
    base = production_base.rstrip("/")
    if "/public_html/" in base:
        suffix = base.split("/public_html/", 1)[1]
        return f"public_html/{suffix}".rstrip("/")
    return base.lstrip("/")


def connect(cfg: dict[str, str]):
    import paramiko

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(
        cfg["SFTP_HOST"],
        username=cfg["SFTP_USER"],
        password=cfg["SFTP_PASSWORD"],
        timeout=30,
        allow_agent=False,
        look_for_keys=False,
    )
    return client, client.open_sftp()


def ensure_remote_dir(sftp, remote_dir: str) -> None:
    remote_dir = remote_dir.replace("\\", "/")
    if not remote_dir or remote_dir in (".", "/"):
        return
    parts = remote_dir.strip("/").split("/")
    path = ""
    for part in parts:
        path = f"{path}/{part}" if path else part
        try:
            sftp.stat(path)
        except OSError:
            sftp.mkdir(path)


def upload(sftp, local: str, remote: str) -> None:
    remote = remote.replace("\\", "/")
    ensure_remote_dir(sftp, posixpath.dirname(remote))
    sftp.put(local, remote)


def delete(sftp, remote: str) -> None:
    remote = remote.replace("\\", "/")
    sftp.remove(remote)


def remote_for(root: str, rel: str) -> str:
    return f"{root}/{rel}".replace("\\", "/")


def local_for(repo: str, rel: str) -> str:
    return os.path.join(repo, rel.replace("/", os.sep))


def run_batch(repo: str, root: str, sftp, batch_file: str) -> int:
    ok = 0
    fail = 0
    with open(batch_file, encoding="utf-8", errors="replace") as f:
        for raw in f:
            line = raw.rstrip("\n\r")
            if not line:
                continue
            try:
                action, rel = line.split("\t", 1)
            except ValueError:
                print(f"FAIL malformed batch line: {line}", file=sys.stderr)
                fail += 1
                continue

            rel = rel.strip().replace("\\", "/").lstrip("/")
            remote = remote_for(root, rel)
            try:
                if action == "UPLOAD":
                    local = local_for(repo, rel)
                    if not os.path.isfile(local):
                        raise FileNotFoundError(local)
                    upload(sftp, local, remote)
                elif action == "DELETE":
                    delete(sftp, remote)
                else:
                    raise ValueError(f"unknown action: {action}")
            except Exception as exc:  # keep batch going and report all failures
                fail += 1
                print(f"FAIL {action.lower()} {rel}: {exc}", file=sys.stderr)
            else:
                ok += 1
                print(f"OK {action.lower()} {rel}")

    print(f"Batch done: OK={ok} FAIL={fail}")
    return 1 if fail else 0


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("action", choices=("upload", "delete", "batch", "test"))
    parser.add_argument("rel", nargs="?", help="repo-relative path")
    parser.add_argument("--repo", default=None)
    parser.add_argument("--batch-file", default=None)
    args = parser.parse_args()

    repo = args.repo or os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    cfg = load_env(repo)
    root = remote_root(cfg["SFTP_PRODUCTION_BASE"])

    if args.action == "upload":
        if not args.rel:
            parser.error("upload requires rel")
        local = local_for(repo, args.rel)
        if not os.path.isfile(local):
            print(f"SKIP missing: {args.rel}", file=sys.stderr)
            return 1
        remote = remote_for(root, args.rel)
    elif args.action == "delete":
        if not args.rel:
            parser.error("delete requires rel")
        remote = remote_for(root, args.rel)
        local = None
    elif args.action == "batch":
        if not args.batch_file:
            parser.error("batch requires --batch-file")
        local = None
        remote = ""
    else:
        local = None
        remote = ""

    import paramiko  # noqa: F401

    client, sftp = connect(cfg)
    try:
        if args.action == "upload":
            upload(sftp, local, remote)
        elif args.action == "delete":
            delete(sftp, remote)
        elif args.action == "batch":
            return run_batch(repo, root, sftp, args.batch_file)
        else:
            print(f"OK connected to {cfg['SFTP_HOST']}")
            return 0
    finally:
        sftp.close()
        client.close()

    print(f"OK {args.rel}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
