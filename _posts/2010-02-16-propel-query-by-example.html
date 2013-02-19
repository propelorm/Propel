---
layout: post
title: Propel Query by Example
published: true
---
<p>If you're used to Criteria and Peer methods, you may find the new query API introduced in Propel 1.5 intimidating. There is nothing to be afraid of: you will find this new API more intuitive and faster to read, write and test, whether you use a text editor or an IDE.<p /> To convince you, there is nothing better than a side-by-side comparison of the same query written with the old and the new API. Without further introduction, let's dive in:<br /><!--more--><br />[code]
/*
 * Retrieving an article by its primary key
 */
// Propel 1.4
$article = ArticlePeer::retrieveByPk(123);
// Propel 1.5
$article = ArticleQuery::create()-&gt;findPk(123);

/*
 * Retrieving the comments related to an article
 */
// Propel 1.4
$comments = $article-&gt;getComments();
// Propel 1.5
$comments = $article-&gt;getComments(); // no change

/*
 * Retrieving an article from its title
 */
// Propel 1.4
$c = new Criteria();
 $c-&gt;add(ArticlePeer::TITLE, 'FooBar');
$article = ArticlePeer::doSelectOne($c);
// Propel 1.5
$article = ArticleQuery::create()-&gt;findOneByTitle('FooBar');

/*
 * Retrieving articles based on a word appearing in the title
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;add(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
 &nbsp;-&gt;filterByTitle('%FooBar%')
 &nbsp;-&gt;find();

/*
 * Retrieving articles where the publication date is between last week and today
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;add(ArticlePeer::PUBLISHED_AT, time() - (7 * 24 * 60 * 60), Criteria::GREATER_THAN);
 $c-&gt;addAnd(ArticlePeer::PUBLISHED_AT, time(), Criteria::LESS_THAN);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
 &nbsp;-&gt;filterByPublishedAt(array(
 &nbsp; &nbsp;'min' =&gt; time() - (7 * 24 * 60 * 60), 
 &nbsp; &nbsp;'max' =&gt; time(),
 &nbsp;))
 &nbsp;-&gt;find();

/*
 * Retrieving articles based on a custom condition
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;add(ArticlePeer::TITLE, 'UPPER(article.TITLE) LIKE ' . $pattern, Criteria::CUSTOM); // risk of SQL injection!!
 $articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
 &nbsp;-&gt;where('UPPER(Article.Title) like ?', '%FooBar%') // binding made by PDO, no injection risk
 &nbsp;find();
 &nbsp;
/*
 * Retrieving articles based on a word appearing in the title or the summary
 */
// Propel 1.4
$c = new Criteria();
$cton1 = $c-&gt;getNewCriterion(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
 $cton2 = $c-&gt;getNewCriterion(ArticlePeer::SUMMARY, '%FooBar%', Criteria::LIKE);
$cton1-&gt;addOr($cton2);
$c-&gt;add($cton1);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
 &nbsp;-&gt;where('Article.Title like ?', '%FooBar%')
 &nbsp;-&gt;orWhere('Article.Summary like ?', '%FooBar%')
 &nbsp;find();

/*
 * Retrieving articles based on a complex AND/OR clause
 * Articles having name or summary like %FooBar% and published between $begin and $end
 */
// Propel 1.4
$c = new Criteria();
$cton1 = $c-&gt;getNewCriterion(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
 $cton1 = $c-&gt;getNewCriterion(ArticlePeer::SUMMARY, '%FooBar%', Criteria::LIKE);
$cton1-&gt;addOr($cton2);
$c-&gt;add($cton1);
$c-&gt;add(ArticlePeer::PUBLISHED_AT, $begin, Criteria::GREATER_THAN);
 $c-&gt;addAnd(ArticlePeer::PUBLISHED_AT, $end, Criteria::LESS_THAN);
 $article = ArticlePeer::doSelect($c);
// Propel 1.5 "Reverse Polish Notation" style
$articles = ArticleQuery::create()
 &nbsp; &nbsp; &nbsp;-&gt;condition('cond1', 'Title like ?', '%FooBar%')
 &nbsp; &nbsp; &nbsp;-&gt;condition('cond2', 'Summary' like ?', '%FooBar%')
 &nbsp; &nbsp; -&gt;combine(array('cond1', 'cond2'), 'or', 'cond3')
 &nbsp; &nbsp; &nbsp;-&gt;condition('cond4', 'PublishedAt &gt; ?', $begin)
 &nbsp; &nbsp; &nbsp;-&gt;condition('cond5', 'PublishedAt &lt; ?', $end)
 &nbsp; &nbsp;-&gt;combine(array('cond4', 'cond5'), 'and', 'cond6')
 &nbsp;-&gt;combine(array('cond3', 'cond6'), 'and')
 &nbsp;-&gt;find();

/*
 * Retrieving the latest 5 articles
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;addDescendingOrderByColumn(ArticlePeer::PUBLISHED_AT);
$c-&gt;setLimit(5);
$articles = ArticlePeer::doSelect($c);
 // Propel 1.5
$articles = ArticleQuery::create()
 &nbsp;-&gt;orderByPublishedAt('desc')
 &nbsp;-&gt;limit(5)
 &nbsp;-&gt;find();

/*
 * Retrieving the last comment related to an article
 */
// Propel 1.4
 $c = new Criteria();
$c-&gt;addDescendingOrderByColumn(CommentPeer::PUBLISHED_AT);
$c-&gt;add(CommentPeer::ARTICLE_ID, $article-&gt;getId());
$comment = CommentPeer::doSelectOne($c);
// Propel 1.5
$comment = CommentQuery::create()
 &nbsp;-&gt;filterByArticle($article)
 &nbsp;-&gt;orderByPublishedAt('desc')
 &nbsp;-&gt;findOne();
 &nbsp;
/*
 * Retrieving articles authored by someone
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;addJoin(ArticlePeer::AUTHOR_ID, AuthorPeer::ID);
 $c-&gt;add(AuthorPeer::NAME, 'John Doe');
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$articles = ArticleQuery::create()
 &nbsp;-&gt;useAuthorQuery()
 &nbsp; &nbsp;-&gt;filterByName('John Doe')
 &nbsp;-&gt;endUse()
 &nbsp;-&gt;find()

/*
 * Retrieving articles authored by people of a certain group
 */
// Propel 1.4
$c = new Criteria();
$c-&gt;addJoin(ArticlePeer::AUTHOR_ID, AuthorPeer::ID);
$c-&gt;addJoin(AuthorPeer::GROUP_ID, GroupPeer::ID);
 $c-&gt;add(GroupPeer::NAME, 'The Foos');
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$articles = ArticleQuery::create()
 &nbsp;-&gt;useAuthorQuery()
 &nbsp; &nbsp;-&gt;useGroupQuery()
 &nbsp; &nbsp; &nbsp;-&gt;filterByName('The Foos')
 &nbsp; &nbsp;-&gt;endUse()
 &nbsp;-&gt;endUse()
 &nbsp;-&gt;find()

/*
 * Retrieving all articles and hydrating their category object in the same query
 */
// Propel 1.4
$c = new Criteria();
$articles = ArticlePeer::doSelectJoinCategory($c);
 // Propel 1.5
$articles = ArticleQuery::create()
 &nbsp;-&gt;joinWith('Category')
 &nbsp;-&gt;find();

/*
 * Retrieving an article and its category by the article primary key
 */
// Propel 1.4
$c = new Criteria();
 $c-&gt;add(ArticlePeer::ID, 123);
$c-&gt;setLimit(1);
$articles = ArticlePeer::doSelectJoinCategory($c);
$article = isset($articles[0]) ? $articles[0] : null;
// Propel 1.5
$articles = ArticleQuery::create()
 &nbsp;-&gt;joinWith('Category')
 &nbsp;-&gt;findPk(123);
[/code]<p />In addition, the new Propel Query API allows for things that were simply not possible with the Criteria and Peer API:<p /><br />[code]
 /*
 * Retrieving articles and hydrating their author object and the author group
 */
$articles = ArticleQuery::create()
 &nbsp;-&gt;joinWith('Article.Author')
 &nbsp;-&gt;joinWith('Author.Group')
 &nbsp;-&gt;find();
 &nbsp;
/*
 * Retrieving articles based on a list of conditions
 */
$conds = array(
 &nbsp;'Title' &nbsp; &nbsp; &nbsp; =&gt; 'Foo',
 &nbsp;'PublishedAt' =&gt; array('min' =&gt; time() - (7 * 24 * 60 * 60))
 );
$articles = ArticleQuery::create()
 &nbsp;-&gt;filterByArray($cond)
 &nbsp;-&gt;find();

/*
 * Retrieving a paginated list of the latest articles
 * Get the second page, 10 articles per page
 */
$articlePager = ArticleQuery::create()
 &nbsp;-&gt;orderByPublishedAt('desc')
 &nbsp;-&gt;paginate(2, 10);
foreach	($articlePager as $article) {
 &nbsp;// do stuff with an $article object
}

/*
 * Iterating over a very large list of results without running out of memory
 */
$articles = ArticleQuery::create()
 &nbsp;-&gt;limit(50000)
 &nbsp;-&gt;useFormatter(ModelCriteria::FORMAT_ON_DEMAND)
 &nbsp;-&gt;find();
foreach	($articles as $article) {
 &nbsp;// do stuff 50,000 times
}
[/code]<p /> There is more for you to discover, and fortunately, the <a href="http://propel.phpdb.org/trac/wiki/Users/Documentation/1.5/ModelCriteria">Propel Query documentation</a> is already up-to-date in the 1.5 branch.</p>
