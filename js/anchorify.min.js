/*!
 * William DURAND <william.durand1@gmail.com>
 * MIT Licensed
 */
(function(b){var a=(function(){var c=/[ ;,.'?!_]/g;function d(f){return f.text().trim().replace(c,"-").replace(/[-]+/g,"-").replace(/-$/,"").toLowerCase()}function e(f){var h=1,g=f;while(0!==b("#"+f).length){f=g+"-"+h++}return f}return{anchorify:function(g){var h=g.text||"¶",i=g.cssClass||"anchor-link",f=g.$el.attr("id")||e(d(g.$el));g.$el.attr("id",f)[g.position||"append"](['<a href="#',f,'" class="',i,'">',h,"</a>"].join(""))}}})();b.fn.anchorify=function(c){this.each(function(){a.anchorify(b.extend({},c||{},{$el:b(this)}))});return this}})(jQuery);