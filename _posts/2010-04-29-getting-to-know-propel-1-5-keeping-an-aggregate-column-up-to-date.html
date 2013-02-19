---
layout: post
title: ! 'Getting To Know Propel 1.5: Keeping An Aggregate Column Up To Date'
published: true
---

<p>Propel 1.5 was released earlier this week, so it’s a good time to learn how to get the most of its new Query objects. Today’s exercise aims to keep an aggregate column up to date, and illustrates the use of ActiveRecord and Query hook methods.</p>

<h3>The Model</h3>
<p>The model is simple: for a poll widget, a <code>PollQuestion</code> and a <code>PollAnswer</code> class share a one-to-many relationship. The <code>PollAnswer</code> class features a <code>NbVotes</code> property, incremented each time a user votes for this question. The <code>PollQuestion</code> also needs a <code>TotalNbVotes</code>, which is the sum of the <code>NbVotes</code> of all the related <code>PollAnswers</code>, in order to display answer ratings as percentages. Let’s see how to manage this <code>TotalNbVotes</code> column automatically.</p>
<p><img src="images/pollER.png" /></p>

<h3>The Easy Part: Using ActiveRecord Hooks</h3>
<p>Each time the <code>PollAnswer</code>’s <code>NbVotes</code> is incremented, the parent <code>PollQuestion</code>’s <code>TotalNbVotes</code> should be incremented as well. In fact, the use case is larger than than: each time a <code>PollAnswer</code> is added, deleted, or modified, the parent’s <code>TotalNbVotes</code> should be recalculated.</p>
<p>Since version 1.4, Propel offers hooks in the generated ActiveRecord model objects, so this is quite easy to implement:</p>
<div class="CodeRay">
<div class="code"><pre>class PollAnswer extends BasePollAnswer
{
    public function postSave(PropelPDO $con)
    {
        if ($parentQuestion = $this-&gt;getPollQuestion()) {
            $parentQuestion-&gt;updateNbVotes($con);
        }
    }

    public function postDelete(PropelPDO $con)  {
        if ($parentQuestion = $this-&gt;getPollQuestion()) {
            $parentQuestion-&gt;updateNbVotes($con);
        }
    }
}
</pre></div>
</div>

<p>Propel calls the <code>postSave()</code> hook each time a PollAnswer object is inserted or updated. Notice that both the <code>postSave()</code> and the <code>postDelete()</code> receive the current connection object, and use it. This is because these methods are called during a transaction, and the connection object should be the same throughout the whole transaction to let Propel and the database revert the transaction is something wrong occurs.</p>

<h3>Good Old PDO To The Rescue</h3>
<p>The task to keep the total vote count up to date is left to the <code>PollQuestion</code> object. This could be done using a <code>ModelCriteria</code>, but since the expected result is a scalar, there is no need to hydrate an ActiveRecord. So let’s use a raw PDO query instead. To keep a minimum of model abstraction, the column and table name should be represented by their class constants:</p>
<div class="CodeRay">
<div class="code"><pre>class PollQuestion extends BasePollQuestion
{
    public function updateNbVotes($con = null)
    {
        $sql = 'SELECT SUM(' . PollAnswerPeer::NB_VOTES . ') AS nb'
            . ' FROM ' . PollAnswerPeer::TABLE_NAME
            . ' WHERE ' . PollAnswerPeer::QUESTION_ID . ' = ?';

        $stmt = $con-&gt;prepare($sql);
        $stmt-&gt;execute(array($this-&gt;getId()));
        $this-&gt;setTotalNbVotes($stmt-&gt;fetchColumn());
        $this-&gt;save($con);
    }
}
</pre></div>
</div>

<p>The <code>updateNbVotes()</code> method executes one SELECT query, and if the result differs from the current TotalNbVotes, then the <code>PollQuestion</code> object gets updated.</p>
<p>So when a <code>PollAnswer</code> gets updated, its <code>NbVotes</code> is saved, then the <code>PollAnswer::postSave()</code> method fetches the parent <code>PollQuestion</code>, and calls <code>PollQuestion::updateNbVotes()</code> to calculate and persist the new <code>TotalNbVotes</code>. All in a single transaction.</p>
<p>That’s it for the easy part.</p>

<h3>Using Query Hooks</h3>
<p>There is a use case that hasn’t been addressed yet: What if a set of <code>PollAnswer</code> objects is deleted using <code>PollAnswerQuery::delete()</code>, rather than using individual <code>PollAnswer::delete()</code> calls? The previous changes wouldn’t be enough to update the <code>TotalNbVotes</code> in this case.</p>
<div class="CodeRay">
<div class="code"><pre>PollAnswerQuery::create()
    -&gt;filterByBody('%TEMP%')
    -&gt;delete();
</pre></div>
</div>

<p>It is necessary to use the <code>preDelete()</code> and <code>postDelete()</code> hooks of the <code>PollAnswerQuery</code> class for that. The <code>preDelete()</code> code must determine the <code>PollAnswer</code> objects concerned by the deletion, and from then on, keep the related <code>PollQuestion</code> objects. The <code>postUpdate()</code> method should iterate over this collection of <code>PollQuestion</code> objects, and call <code>updateNbVotes()</code> on each of them. Here is a first way to implement this:</p>
<div class="CodeRay">
<div class="code"><pre>class PollAnswerQuery extends BasePollAnswerQuery
{
    protected $pollQuestions = array();

    public function preDelete(PropelPDO $con) {
        $pollAnswerQuery = clone $this;
        $pollAnswers = $pollAnswerQuery
            -&gt;joinWith('PollQuestion')
            -&gt;find($con);

        foreach ($pollAnswers as $pollAnswer) {
            $this-&gt;pollQuestions[$pollAnswer-&gt;getQuestionId()] = $pollAnswer-&gt;getPollQuestion();
        }
    }

    public function postDelete($affectedRows, PropelPDO $con) {
        foreach ($this-&gt;pollQuestions as $pollQuestion) {
            $pollQuestion-&gt;updateVotesNb($con);
        }
        $this-&gt;pollQuestions = array();
    }
}
</pre></div>
</div>

<p>The <code>preDelete()</code> code reuses the current query object, but terminates with a <code>find()</code> rather than a <code>delete()</code>. <code>joinWith()</code> helps to reduce the number of SQL queries to 1, even though <code>getPollQuestion()</code> gets called in a loop afterwards.</p>
<h3>Merging Queries</h3>
<p>The <code>preDelete()</code> code creates a SQL query similar to:</p>
<div class="CodeRay">
<div class="code"><pre>
SELECT poll_answer.*, poll_question.*FROM poll_answer
INNER JOIN poll_question ON poll_answer.question_id = poll_question.id
WHERE poll_answer.body LIKE '%TEMP%';</pre></div>
</div>

<p>The PHP code iterates over the result of this query to retrieve a list of <code>PollQuestion</code> objects with no duplicates. But what is really needed there is a list of <code>PollQuestion</code> objects. There is no real need to pass by an intermediate list of <code>PollAnswers</code>.</p>
<p>But, if you use a <code>PollQuestionQuery</code> to get <code>PollQuestion</code> objects, how can it take the conditions applied to a <code>PollAnswerQuery</code> object? It’s quite simple: just <em>merge</em> the two query objects together. You can simply write the <code>PollAnswerQuery::preDelete()</code> method as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>public function preDelete(PropelPDO $con)
{
    $this-&gt;pollQuestions = PollQuestionQuery::create()
        -&gt;joinPollAnswer()
        -&gt;mergeWith($this)
        -&gt;find($con);
}
</pre></div>
</div>

<p>The resulting SQL query is now:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT poll_question.*FROM poll_question INNER JOIN poll_answerON poll_question.id = poll_answer.question_idWHERE poll_answer.body LIKE '%TEMP%';</pre></div>
</div>

<h3>Conclusion</h3>
<p>Query objects are very reusable: you can clone them and use a different termination method, or merge them with another query. It brings reusability to queries the same way the ActiveRecord pattern brings reusability to row manipulation.</p>
<p>The example is not finished: the <code>PollAnswerQuery</code> method should also implement the <code>preUpdate()</code> and <code>postUpdate()</code> hooks to deal with <code>update()</code> queries that may alter the vote count of a list of <code>PollAnswer</code> objects.</p>
<p>And, besides polls, what was demonstrated here is a very common need: keep an column calculated with an aggregate function on a related table up to date. From the number of books of an author to the latest edition of the articles of a website, the requirement is very generic. To make the above code even more reusable, it must be packaged as a behavior. You will learn how to create such a <code>aggregate_column</code> behavior in a future session of "<em>Getting To Know Propel 1.5</em>".</p>
