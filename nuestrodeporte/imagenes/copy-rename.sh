echo "From: $1";
echo "To: $2";

for file in $1-*
do
   name="${file%.*}"
   extension="${file##*.}"
   cp $file $2${name}.${extension}
done
rename $2$1 $2 $2$1-*

cd Original
for file in $1-*
do
   name="${file%.*}"
   extension="${file##*.}"
   cp $file $2${name}.${extension}
done
rename $2$1 $2 $2$1-*
