---
layout: post
title: Introducing ExtraPropertiesBehavior
published: true
---
<p>Hi all,</p>
<p>when I released <a href="https://github.com/Carpe-Hora/ExtraPropertiesBehavior">ExtraPropertiesBehavior</a> on github, William asked me to introduce it on the propel blog to make sure everybody is aware it exists. I'm definitely not used to blog, so I'll try to do my best.</p>
<p>Sometimes in a dev's life you have to store fields you did not or can not plan about, that's usualy what happens when a model can be extended through plugins, but also for user preferences or configuration. To handle this kind of extension, the best way is a key/value table related to your model.</p>
<p>Most of use cases do not require this kind of behavior as joining with a  key/value table can slow down queries, so always make sure that <span style="text-decoration: underline;">delegate</span> or <span style="text-decoration: underline;">concrete inheritance</span> cannot solve your problem but sometimes there is no other way. If you already had to manage such things, you probably know it can be really painful.</p>
<p>Not anymore, thanks to <a href="https://github.com/Carpe-Hora/ExtraPropertiesBehavior">ExtraPropertiesBehavior</a>, all you need is this line in your model declaration : <span class="nt">&lt;behavior</span> <span class="na">name=</span><span class="s">"extra_properties"</span> <span class="nt">/&gt;, rebuild the model, and you're done !</span></p>
<p><span class="nt">From now on, declare, get or set properties with dedicated methods, you even can handle simple collections. Behavior is fully documented in the README file, so no more hesitation, fork me on github ;)</span></p>
<p><span class="nt">Cheers</span></p>
