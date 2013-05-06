---
layout: post
title: propel.phpdb.org has shut down, long live propelorm.org!
published: true
---
<div>The long awaited server switch finally took place today. We changed the Propel server, domain name, and website design, mostly to solve the speed and stability problems of the previous host. The new Propel website is <a href="http://www.propelorm.org" target="_blank">http://www.propelorm.org</a>, and you're welcome to visit it anytime. It still has few rough edges, but with your feedback and a few days of polish, it will be almost perfect!</div>
<p />
<div>The new website emphasizes the installation and documentation sections. It's still based on Trac, but we managed to make Trac a little more discreet in the documentation pages. Of course, all the Trac history, tickets and milestones from the <a href="http://phpdb.org" target="_blank">phpdb.org</a> server were imported, and you will find the ticketing, source and timeline under the '<a href="http://www.propelorm.org/timeline" target="_blank">Dev</a>' section. We couldn't import the old user accounts from the previous site, but the good news is that it is now easy to register directly in the 'Dev' section. If you had a user account, open a new account at <a href="http://propelorm.org" target="_blank">propelorm.org</a> with the same username, and you should receive alerts on the tickets you've opened. If you had commit access, you'll have to ask me again.</div>
<p />
<div>The subversion server has also moved. The old server, available at <a href="http://svn.phpdb.org/propel" target="_blank">http://svn.phpdb.org/propel</a>, is still accessible, but it's now read only, and the next changes won't appear there. The new Propel SVN server address is <a href="http://svn.propelorm.org" target="_blank">http://svn.propelorm.org</a>. It has been synchronized with the previous server. You'll need to change your svn externals, or relocate your existing checkouts:</div>
<p />
<div>  &gt; svn switch --relocate <a href="http://svn.phpdb.org/propel" target="_blank">http://svn.phpdb.org/propel</a> <a href="http://svn.propelorm.orm" target="_blank">http://svn.propelorm.org</a>
</div>
<p />
<div>The PEAR server will remain at the previous address for a short period of time. We'll soon offer a pear channel at <a href="http://pear.propelorm.org" target="_blank">pear.propelorm.org</a>.</div>
<p />
<div>The blog URL is unchanged - <a href="http://propel.posterous.com" target="_blank">http://propel.posterous.com</a>.</div>
<p />
<div>The old server should redirect most of the documentation and ticket pages to the new server, page to page. However, search engines may take time to update their index, so mentioning the new Propel website url in your own sites may speed up re-crawl.</div>
<p />
<div>I'd like to thank Veikko Mäkinen, for giving the domain name and designing the new website, and Benjamin Boerngen-Schmidt, for providing the server, adapting the design to Trac, transferring all the data from the old server, and administering the new server.</div>
<p />
<div>Of course, your feedback is welcome!</div>
