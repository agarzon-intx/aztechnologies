for file in 11-*
do
   name="${file%.*}"
   extension="${file##*.}"
   cp $file 12${name}.${extension}
done
rename 1211 12 1211-*

