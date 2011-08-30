#!/bin/sh

if [ -z $1 ] ; then
    echo "Aborted."
    exit 1
fi

sed -i  's/======/######/g' $1
sed -i  's/=====/#####/g' $1
sed -i  's/====/####/g' $1
sed -i  's/===/###/g' $1
sed -i  's/^==/##/g' $1
sed -i  's/==$/##/g' $1
sed -i  's/^=/#/g' $1
sed -i  's/=$/#/g' $1

sed -i  "s/'''Tip''': />\*\*Tip\*\*<br \/>/g" $1

sed -i -e 's/{{{\n#!/{% highlight /g' $1
sed -i 's/}}}/{% endhighlight %}/g' $1

sed -i -e 's/\[\[PageOutline\]\]\n//' $1

sed -i "s/''/_/g" $1

sed -i 's/highlight php$/highlight php %}/' $1
sed -i 's/highlight sh$/highlight bash %}/' $1
sed -i 's/highlight bash$/highlight bash %}/' $1
sed -i 's/highlight ini$/highlight ini %}/' $1
sed -i 's/highlight sql$/highlight sql %}/' $1
sed -i 's/highlight xml$/highlight xml %}/' $1
