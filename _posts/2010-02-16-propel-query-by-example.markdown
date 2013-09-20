---
layout: post
title: Propel Query by Example
published: true
---

If you're used to Criteria and Peer methods, you may find the new query API introduced in Propel 1.5 intimidating. There is nothing to be afraid of: you will find this new API more intuitive and faster to read, write and test, whether you use a text editor or an IDE.<p /> To convince you, there is nothing better than a side-by-side comparison of the same query written with the old and the new API. Without further introduction, let's dive in:

```php
/*
 * Retrieving an article by its primary key
 */
// Propel 1.4
$article = ArticlePeer::retrieveByPk(123);
// Propel 1.5
$article = ArticleQuery::create()->findPk(123);

/*
 * Retrieving the comments related to an article
 */
// Propel 1.4
$comments = $article->getComments();
// Propel 1.5
$comments = $article->getComments(); // no change

/*
 * Retrieving an article from its title
 */
// Propel 1.4
$c = new Criteria();
$c->add(ArticlePeer::TITLE, 'FooBar');
$article = ArticlePeer::doSelectOne($c);
// Propel 1.5
$article = ArticleQuery::create()->findOneByTitle('FooBar');

/*
 * Retrieving articles based on a word appearing in the title
 */
// Propel 1.4
$c = new Criteria();
$c->add(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
  ->filterByTitle('%FooBar%')
  ->find();

/*
 * Retrieving articles where the publication date is between last week and today
 */
// Propel 1.4
$c = new Criteria();
$c->add(ArticlePeer::PUBLISHED_AT, time() - (7 * 24 * 60 * 60), Criteria::GREATER_THAN);
$c->addAnd(ArticlePeer::PUBLISHED_AT, time(), Criteria::LESS_THAN);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
  ->filterByPublishedAt(array(
    'min' => time() - (7 * 24 * 60 * 60),
    'max' => time(),
  ))
  ->find();

/*
 * Retrieving articles based on a custom condition
 */
// Propel 1.4
$c = new Criteria();
$c->add(ArticlePeer::TITLE, 'UPPER(article.TITLE) LIKE ' . $pattern, Criteria::CUSTOM); // risk of SQL injection!!
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
  ->where('UPPER(Article.Title) like ?', '%FooBar%') // binding made by PDO, no injection risk
  ->find();

/*
 * Retrieving articles based on a word appearing in the title or the summary
 */
// Propel 1.4
$c = new Criteria();
$cton1 = $c->getNewCriterion(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
$cton2 = $c->getNewCriterion(ArticlePeer::SUMMARY, '%FooBar%', Criteria::LIKE);
$cton1->addOr($cton2);
$c->add($cton1);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$article = ArticleQuery::create()
  ->where('Article.Title like ?', '%FooBar%')
  ->orWhere('Article.Summary like ?', '%FooBar%')
  ->find();

/*
 * Retrieving articles based on a complex AND/OR clause
 * Articles having name or summary like %FooBar% and published between $begin and $end
 */
// Propel 1.4
$c = new Criteria();
$cton1 = $c->getNewCriterion(ArticlePeer::TITLE, '%FooBar%', Criteria::LIKE);
$cton1 = $c->getNewCriterion(ArticlePeer::SUMMARY, '%FooBar%', Criteria::LIKE);
$cton1->addOr($cton2);
$c->add($cton1);
$c->add(ArticlePeer::PUBLISHED_AT, $begin, Criteria::GREATER_THAN);
$c->addAnd(ArticlePeer::PUBLISHED_AT, $end, Criteria::LESS_THAN);
$article = ArticlePeer::doSelect($c);
// Propel 1.5 "Reverse Polish Notation" style
$articles = ArticleQuery::create()
       ->condition('cond1', 'Title like ?', '%FooBar%')
       ->condition('cond2', 'Summary' like ?', '%FooBar%')
     ->combine(array('cond1', 'cond2'), 'or', 'cond3')
       ->condition('cond4', 'PublishedAt > ?', $begin)
       ->condition('cond5', 'PublishedAt &lt; ?', $end)
     ->combine(array('cond4', 'cond5'), 'and', 'cond6')
  ->combine(array('cond3', 'cond6'), 'and')
  ->find();

/*
 * Retrieving the latest 5 articles
 */
// Propel 1.4
$c = new Criteria();
$c->addDescendingOrderByColumn(ArticlePeer::PUBLISHED_AT);
$c->setLimit(5);
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$articles = ArticleQuery::create()
  ->orderByPublishedAt('desc')
  ->limit(5)
  ->find();

/*
 * Retrieving the last comment related to an article
 */
// Propel 1.4
 $c = new Criteria();
$c->addDescendingOrderByColumn(CommentPeer::PUBLISHED_AT);
$c->add(CommentPeer::ARTICLE_ID, $article->getId());
$comment = CommentPeer::doSelectOne($c);
// Propel 1.5
$comment = CommentQuery::create()
  ->filterByArticle($article)
  ->orderByPublishedAt('desc')
  ->findOne();

/*
 * Retrieving articles authored by someone
 */
// Propel 1.4
$c = new Criteria();
$c->addJoin(ArticlePeer::AUTHOR_ID, AuthorPeer::ID);
$c->add(AuthorPeer::NAME, 'John Doe');
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$articles = ArticleQuery::create()
  ->useAuthorQuery()
    ->filterByName('John Doe')
  ->endUse()
  ->find()

/*
 * Retrieving articles authored by people of a certain group
 */
// Propel 1.4
$c = new Criteria();
$c->addJoin(ArticlePeer::AUTHOR_ID, AuthorPeer::ID);
$c->addJoin(AuthorPeer::GROUP_ID, GroupPeer::ID);
$c->add(GroupPeer::NAME, 'The Foos');
$articles = ArticlePeer::doSelect($c);
// Propel 1.5
$articles = ArticleQuery::create()
  ->useAuthorQuery()
    ->useGroupQuery()
      ->filterByName('The Foos')
    ->endUse()
  ->endUse()
  ->find()

/*
 * Retrieving all articles and hydrating their category object in the same query
 */
// Propel 1.4
$c = new Criteria();
$articles = ArticlePeer::doSelectJoinCategory($c);
// Propel 1.5
$articles = ArticleQuery::create()
  ->joinWith('Category')
  ->find();

/*
 * Retrieving an article and its category by the article primary key
 */
// Propel 1.4
$c = new Criteria();
$c->add(ArticlePeer::ID, 123);
$c->setLimit(1);
$articles = ArticlePeer::doSelectJoinCategory($c);
$article = isset($articles[0]) ? $articles[0] : null;
// Propel 1.5
$articles = ArticleQuery::create()
  ->joinWith('Category')
  ->findPk(123);
```

In addition, the new Propel Query API allows for things that were simply not possible with the Criteria and Peer API:

```php
/*
 * Retrieving articles and hydrating their author object and the author group
 */
$articles = ArticleQuery::create()
  ->joinWith('Article.Author')
  ->joinWith('Author.Group')
  ->find();

/*
 * Retrieving articles based on a list of conditions
 */
$conds = array(
  'Title'       => 'Foo',
  'PublishedAt' => array('min' => time() - (7 * 24 * 60 * 60))
 );
$articles = ArticleQuery::create()
  ->filterByArray($cond)
  ->find();

/*
 * Retrieving a paginated list of the latest articles
 * Get the second page, 10 articles per page
 */
$articlePager = ArticleQuery::create()
  ->orderByPublishedAt('desc')
  ->paginate(2, 10);
foreach	($articlePager as $article) {
  // do stuff with an $article object
}

/*
 * Iterating over a very large list of results without running out of memory
 */
$articles = ArticleQuery::create()
  ->limit(50000)
  ->useFormatter(ModelCriteria::FORMAT_ON_DEMAND)
  ->find();
foreach	($articles as $article) {
  // do stuff 50,000 times
}
```

There is more for you to discover, and fortunately, the <a href="http://propel.phpdb.org/trac/wiki/Users/Documentation/1.5/ModelCriteria">Propel Query documentation</a> is already up-to-date in the 1.5 branch.</p>
