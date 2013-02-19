---
layout: post
title: ! 'Don''t Do This At Home #5: Use where() Instead Of filterBy()'
published: true
---
<p>Can you spot the problem in the following snippet?</p>
<p><script src="https://gist.github.com/cf42a7131f1246f498b9.js"></script></p>
<p><!--more-->Firstly, this method can only be used if the query was created using the default class name. Remember: the developer can use an alias instead of the full model class name when creating a query:</p>
<p><script src="https://gist.github.com/ac2c1fd2417004c1e527.js"></script></p>
<p>This is useful when dealing with long classnames. Also, Propel automatically aliases a query when it is embedded into another one with <code>useXXXQuery()</code>. That means Propel may not recognize the column name <code>Book.PublishedAt</code> if the query was created using an alias. The correct way to implement a call to <code>where()</code> in a query method is to use <code>ModelCriteria::getModelAliasOrName()</code>, as follows:</p>
<p><script src="https://gist.github.com/ee53142335c95476da3a.js"></script></p>
<p>Secondly, calling <code>where($clause, $value)</code> triggers a parsing of the <code>$clause</code> string. Propel looks for column names in the string to determine the binding type to use for the value. In this example, <code>'Book.PublishedAt'</code> refers to a <code>TIMESTAMP</code> column, so Propel converts the <code>$value</code> to a timestamp string and binds it as a string. This clause parsing process uses regular expressions and has an impact on performance at runtime.</p>
<p>The alternative is to use the generated <code>filterByXXX()</code> method. When generating the query class, Propel knows the type of each column, and creates filter methods with the correct binding type baked in. Therefore, the execution of a <code>filterByXXX()</code> method requires no string parsing, and provides additional features. For instance, for date and time columns, the generated filter method allows for array values, which is especially useful in the <code>filterByPublishedAtBetween()</code> case:</p>
<p><script src="https://gist.github.com/e6a6adbc96d573c851f3.js"></script></p>
<p>And while we are at it, you don't have to name all your filter methods "filterBySomething". Propel uses this convention for column filters, but your custom filters should be named after the business rule they represent. So keep them as short and expressive as possible. In the <code>BookQuery</code> example, it's probably a better idea to rename <code>filterByPublishedAtBetween($begin, $end)</code> to <code>publishedBetween($begin, $end)</code>. That way, you can create very expressive queries:</p>
<p><script src="https://gist.github.com/6737827e5a3fcafcc482.js"></script></p>
