<?php
    set_include_path($_SERVER['DOCUMENT_ROOT'] . '/includes');
    $page_title = 'Developers';
    include ('header.php');
?>

<h1 id="writing-tests-for-quantecon">Writing Tests for QuantEcon</h1>
<h2 id="summary">Summary</h2>
<p>The QuantEcon package uses <a href="https://nose.readthedocs.org/en/latest/">Nose</a> to manage tests. We adhere to some conventions to facilitate ease of maintenance and management of test cases. The main conventions are as follows:</p>
<ol style="list-style-type: decimal">
<li>Tests live in Python files, which in turn live in <code>quantecon/tests/</code></li>
<li>These test files should be prepended with <code>test_</code> followed by module name (e.g., tests for <code>asset_pricing.py</code> should be found in <code>quantecon/tests/test_asset_pricing.py</code>).</li>
<li>Test files may contain either traditional Python <code>unittest</code> classes or the simpler <code>def test_function()</code> style tests made available by Nose.</li>
</ol>
<p>If you use Nose style test functions, please refer to the test name conventions discussed below so that nose can identify them as tests.</p>
<h2 id="test-fundamentals">Test Fundamentals</h2>
<p>The basic premise of testing in Python is to write functions that make assertions. An assertion checks a given logical condition. If the condition is met, then the program continues onto the next line. If not, the assertion will trigger an Exception and issue an <code>AssertionError</code>. Here's a simple example:</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="kw">def</span> test_equal(a,b):
    <span class="co">&#39;&#39;&#39;</span>
<span class="co">    # Communicate what this Test Does In the DocString #</span>
<span class="co">    This tests for equality between two arguments a and b</span>
<span class="co">    &#39;&#39;&#39;</span>
    <span class="kw">assert</span> a==b, <span class="st">&quot;Test failed: Arguments are not equal.&quot;</span> </code></pre>
<p>If <code>a=2</code> and <code>b=1</code> then this test would fail and raise an <code>AssertionError</code> with the the message string after the comma.</p>
<h3 id="running-tests">Running Tests</h3>
<p>Nose parses Python files in the QuantEcon repository and collects all tests (i.e., all functions that satisfy the test naming convention discussed below). It then runs them one by one and provides you with a report of which passed and which failed.</p>
<p>To run the test suite, you need to type <code>nosetests</code> at the command line, or <code>nosetests -v</code> for a more verbose report.</p>
<h3 id="test-function-names">Test Function Names</h3>
<p>Perhaps the easiest way to write basic tests is using Nose test functions. When <code>nose</code> parses the repository looking for tests it will search for the following regular expression: <code>?:^|[\\b_\\.-])[Tt]est</code>. What this means is that your function name must contain <code>test</code> or <code>Test</code> either at a word boundary after an underscore or hyphen. Examples:</p>
<ul>
<li><code>test_somethinghere</code></li>
<li><code>somethinghere_test</code></li>
<li><code>TestSomethingHere</code></li>
</ul>
<p>When naming your test function or class, remember to use PEP8 convention, as reading files that are similarly formated is less tiresome. Also make sure your clearly indicate what part of a module (be it a function or class etc.) that your test suite is testing.</p>
<h3 id="assertion-methods">Assertion Methods</h3>
<p>While it's fine to construct your own logic and messaging using <code>assert</code> statements as above, note that there are also many helpful pre-existing assertion methods available in other packages, such as</p>
<ol style="list-style-type: decimal">
<li><a href="https://docs.python.org/2/library/unittest.html#assert-methods">unittest</a></li>
<li><a href="http://docs.scipy.org/doc/numpy/reference/routines.testing.html">numpy.testing</a></li>
<li><a href="https://github.com/pydata/pandas/blob/master/pandas/util/testing.py">pandas.util</a></li>
</ol>
<p>These packages are used throughout QuantEcon, so it is safe to retrieved functions and methods from them directly using import statements such as:</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="ch">from</span> numpy.testing <span class="ch">import</span> assert_allclose</code></pre>
<p>This particular function is useful in testing if an array matches a known solution, allowing for a degree of tolerance through the <code>rtol=</code> relative tolerance or <code>atol=</code> absolute tolerance keyword arguments.</p>
<h3 id="next-steps">Next Steps</h3>
<p>To learn more, you can either read on below or browse some of the files in <code>quantecon/tests/</code> and learn from these examples.</p>
<h2 id="example-1-a-basic-test">Example 1: A Basic Test</h2>
<p>Now let's now look at an extended example, concerning a basic test for the <code>mc_compute_startionary</code> function from the <code>mc_tools.py</code> module.<br />We will use a known matrix and compute it's stationary distribution.</p>
<p><br /><span class="math">$$$ 
P = \begin{matrix}
        0.4 &amp; 0.6 \\
        0.2 &amp; 0.8
    \end{matrix}
$$</span><br />$</p>
<p>We know that the unique stationary distribution should be:</p>
<p><br /><span class="math">$$$
\begin{matrix}
    0.25 \\
    0.75
\end{matrix}
$$</span><br />$</p>
<p>Now let's write a test case in the file: <code>tests/test_mc_tools.py</code> and have a look at the results.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="co"># Check required infrastructure is imported</span>
<span class="ch">import</span> numpy <span class="ch">as</span> np
<span class="ch">from</span> numpy.testing <span class="ch">import</span> assert_array_equal

<span class="co"># Check that the test_mc_tools.py file has imported the relevant function we wish to test: mc_compute_stationary</span>
<span class="ch">from</span> quantecon <span class="ch">import</span> mc_compute_stationary

<span class="kw">def</span> test_mc_compute_stationary_pmatrix():
    <span class="co">&quot;&quot;&quot;</span>
<span class="co">    Test for a Known Solution </span>
<span class="co">    Module:     mc_tools.py </span>
<span class="co">    Function:   mc_compute_stationary</span>
<span class="co">    &quot;&quot;&quot;</span>
    P = np.array([[<span class="fl">0.4</span>,<span class="fl">0.6</span>], [<span class="fl">0.2</span>,<span class="fl">0.8</span>]])
    P_known = np.array([<span class="fl">0.25</span>, <span class="fl">0.75</span>])
    computed = mc_compute_stationary(P)
    assert_array_equal(computed, P_known)</code></pre>
<p>Running this test returns</p>
<pre class="sourceCode python"><code class="sourceCode python">---------------------------------------------------------------------------
<span class="ot">AssertionError</span>                            Traceback (most recent call last)
<span class="co">#Traceback details are presented here</span>

<span class="ot">AssertionError</span>: 
Arrays are not equal

(mismatch <span class="fl">50.0</span>%)
 x: array([ <span class="fl">0.25</span>,  <span class="fl">0.75</span>])
 y: array([ <span class="fl">0.25</span>,  <span class="fl">0.75</span>])</code></pre>
<p>This test actually fails! Why? This is because computed results and perfect analytical results are often very close but not quite equal. Let's take a look at what the variable <code>computed</code> looks like in this case (by returning it and having a look using IPython) :</p>
<pre class="sourceCode python"><code class="sourceCode python">In [<span class="dv">1</span>]: computed
Out[<span class="dv">1</span>]: array([ <span class="fl">0.25</span>,  <span class="fl">0.75</span>])
In [<span class="dv">2</span>]: computed[<span class="dv">0</span>]
Out[<span class="dv">2</span>]: <span class="fl">0.24999999999999994</span>
In [<span class="dv">3</span>]: computed[<span class="dv">1</span>]
Out[<span class="dv">3</span>]: <span class="fl">0.75</span>
In [<span class="dv">4</span>]: computed == known
Out[<span class="dv">4</span>]: array([<span class="ot">False</span>,  <span class="ot">True</span>], dtype=<span class="dt">bool</span>)</code></pre>
<p>As you can see the test results really are the same and numerical exactness in computing the results in this case is an issue. That's why numpy.testing also has asserts such as <code>assert_allclose</code> where you can set a relative tolerance and absolute tolerance through the keyword arguments <code>rtol=</code> and <code>atol=</code> (default values: rtol=1e-07, atol=0)</p>
<p>Updating the test to make use of <code>assert_allclose</code> will produce the expected result due to the small difference in relative values.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="co"># Check required infrastructure is imported</span>
<span class="ch">import</span> numpy <span class="ch">as</span> np
<span class="ch">from</span> numpy.testing <span class="ch">import</span> assert_allclose

<span class="co"># Check that the test_mc_tools.py file has imported the relevant function we wish to test: mc_compute_stationary</span>
<span class="ch">from</span> quantecon <span class="ch">import</span> mc_compute_stationary

<span class="kw">def</span> test_mc_compute_stationary_pmatrix():
    <span class="co">&quot;&quot;&quot;</span>
<span class="co">    Test mc_compute_stationary for a Known Solution of Matrix P</span>
<span class="co">    Module:     mc_tools.py </span>
<span class="co">    Function:   mc_compute_stationary</span>
<span class="co">    &quot;&quot;&quot;</span>
    P = np.array([[<span class="fl">0.4</span>,<span class="fl">0.6</span>], [<span class="fl">0.2</span>,<span class="fl">0.8</span>]])
    P_known = np.array([<span class="fl">0.25</span>, <span class="fl">0.75</span>])
    computed = mc_compute_stationary(P)
    assert_allclose(computed, P_known)</code></pre>
<h3 id="making-this-test-more-general">Making this test more General</h3>
<p>Other considerations to testing include making useful test functions that can generalise. For example, to make this test a bit more usable with a larger set of P Matrices, you may want to update the test function by allowing arguments which might accept a tuple of data and the known solution <code>test_set_1 = (P, know)</code>. Now others can also make use of this test if they want to add another (or special case) P Matrix and associated known solution by looping over a list of tuples. A simple update to this test would then look like:</p>
<pre class="sourceCode python"><code class="sourceCode python">
<span class="kw">def</span> test_mc_compute_stationary_pmatrix():
    testset1 = (np.array([[<span class="fl">0.4</span>,<span class="fl">0.6</span>], [<span class="fl">0.2</span>,<span class="fl">0.8</span>]]), np.array([<span class="fl">0.25</span>, <span class="fl">0.75</span>]))       
    check_mc_compute_stationary_pmatrix(testset1)

<span class="kw">def</span> check_mc_compute_stationary_pmatrix(testset):
    <span class="co">&quot;&quot;&quot;</span>
<span class="co">    Test mc_compute_stationary for a Known Solution of Matrix P</span>
<span class="co">    Module:     mc_tools.py </span>
<span class="co">    Function:   mc_compute_stationary</span>
<span class="co">    </span>
<span class="co">    Arguments</span>
<span class="co">    ---------</span>
<span class="co">    [1] test_set    :   tuple(np.array(P), np.array(known_solution))</span>
<span class="co">    &quot;&quot;&quot;</span>
    (P, known) = testset
    computed = mc_compute_stationary(P)
    assert_allclose(computed, known)</code></pre>
<h2 id="example-2-an-extended-example">Example 2: An Extended Example</h2>
<p>As a more extended example, we will make use of <code>mc_tools.py</code> and write some tests for the <code>mc_compute_stationary</code> function that requires some setup prior to running a test. This test is constructed from an example written by https://github.com/oyamad/test_mc_compute_stationary and compares three different approaches to demonstrate some benefits to using classes to organise the tests. As you will see in this example one big advantage to using classes is that you can specify <code>setUp</code> and <code>tearDown</code> functions which ensure each test is run in a consistent environment and state.</p>
<p>So let's setup our test file (assuming it didn't already exist) which we would call <code>test_mc_tools.py</code> and place it in the <code>tests/</code> directory:</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="co">&quot;&quot;&quot;</span>
<span class="co">Tests for mc_tools.py</span>

<span class="co">Functions</span>
<span class="co">---------</span>
<span class="co">    mc_compute_stationary</span>
<span class="co">&quot;&quot;&quot;</span>

<span class="ch">from</span> __future__ <span class="ch">import</span> division

<span class="ch">import</span> numpy <span class="ch">as</span> np
<span class="ch">import</span> unittest

<span class="co"># Tests: mc_compute_stationary #</span>
<span class="co">################################</span>

<span class="ch">from</span> ..mc_tools <span class="ch">import</span> mc_compute_stationary    <span class="co"># An example of using relative references within a package #</span></code></pre>
<p>Sometimes Supporting Test Functions may be required for Generating Markov Matrices such as the KMR Model. However more often then not these <code>support</code> functions can be imported from the project. This can make it clearer regarding what is actually acting as <code>input</code> into the test cases.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="kw">def</span> KMRMarkovMatrixSequential(N, p, epsilon):
    <span class="co">&quot;&quot;&quot;</span>
<span class="co">    Generate the Markov matrix for the KMR model with *sequential* move</span>

<span class="co">    N: number of players</span>
<span class="co">    p: level of p-dominance for action 1</span>
<span class="co">       = the value of p such that action 1 is the BR for (1-q, q) for any q &gt; p,</span>
<span class="co">         where q (1-q, resp.) is the prob that the opponent plays action 1 (0, resp.)</span>
<span class="co">    epsilon: mutation probability</span>

<span class="co">    References: </span>
<span class="co">        KMRMarkovMatrixSequential is contributed from https://github.com/oyamad</span>
<span class="co">    &quot;&quot;&quot;</span>
    P = np.zeros((N<span class="dv">+1</span>, N<span class="dv">+1</span>), dtype=<span class="dt">float</span>)
    P[<span class="dv">0</span>, <span class="dv">0</span>], P[<span class="dv">0</span>, <span class="dv">1</span>] = <span class="dv">1</span> - epsilon * (<span class="dv">1</span>/<span class="dv">2</span>), epsilon * (<span class="dv">1</span>/<span class="dv">2</span>)
    <span class="kw">for</span> n in <span class="dt">range</span>(<span class="dv">1</span>, N):
        P[n, n<span class="dv">-1</span>] = \
            (n/N) * (epsilon * (<span class="dv">1</span>/<span class="dv">2</span>) +
                     (<span class="dv">1</span> - epsilon) * (((n<span class="dv">-1</span>)/(N<span class="dv">-1</span>) &lt; p) + ((n<span class="dv">-1</span>)/(N<span class="dv">-1</span>) == p) * (<span class="dv">1</span>/<span class="dv">2</span>))
                     )
        P[n, n<span class="dv">+1</span>] = \
            ((N-n)/N) * (epsilon * (<span class="dv">1</span>/<span class="dv">2</span>) +
                         (<span class="dv">1</span> - epsilon) * ((n/(N<span class="dv">-1</span>) &gt; p) + (n/(N<span class="dv">-1</span>) == p) * (<span class="dv">1</span>/<span class="dv">2</span>))
                         )
        P[n, n] = <span class="dv">1</span> - P[n, n<span class="dv">-1</span>] - P[n, n<span class="dv">+1</span>]
    P[N, N<span class="dv">-1</span>], P[N, N] = epsilon * (<span class="dv">1</span>/<span class="dv">2</span>), <span class="dv">1</span> - epsilon * (<span class="dv">1</span>/<span class="dv">2</span>)
    <span class="kw">return</span> P</code></pre>
<p><strong>Note:</strong> In production code - there should also be tests for the above function to ensure it is producing expected results given N, p, and epsilon.</p>
<h3 id="using-unittest.testcase-framework">Using <code>unittest.TestCase</code> Framework</h3>
<p><code>unittest.TestCase</code> is a class provided by the python <code>unittest</code> module. By constructing a class instance using inheritance of the <code>TestCase</code> class, we inherit a number of useful methods. However it does specify some conventions that need to be used to make it all work. A test setup method needs to be located in a method called: <code>def setUp(self)</code> and a test teardown methods needs to be located in a method called: <code>def tearDown(self)</code>. Specifying these methods ensures a common setup is performed prior to running each test. This relocates code from each test function and reduces the chances of error.</p>
<p>Some benefits to inheriting <code>unittest.TestCase</code> includes the inbuilt support for some assert methods like <code>self.assertEqual()</code> etc.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="co"># Construct a Class </span>
<span class="kw">class</span> TestMcComputeStationaryKMRMarkovMatrix(unittest.TestCase):
    <span class="co">&#39;&#39;&#39;</span>
<span class="co">        Test Suite for mc_compute_stationary using KMR Markov Matrix [using unittest.TestCase]</span>
<span class="co">    &#39;&#39;&#39;</span>

    <span class="co"># Starting Values #</span>

    N = <span class="dv">27</span>
    epsilon = <span class="fl">1e-2</span>
    p = <span class="dv">1</span>/<span class="dv">3</span>
    TOL = <span class="fl">1e-2</span>

    <span class="kw">def</span> setUp(<span class="ot">self</span>):
        <span class="ot">self</span>.P = KMRMarkovMatrixSequential(<span class="ot">self</span>.N, <span class="ot">self</span>.p, <span class="ot">self</span>.epsilon)
        <span class="ot">self</span>.v = mc_compute_stationary(<span class="ot">self</span>.P)

    <span class="kw">def</span> test_markov_matrix(<span class="ot">self</span>):
        <span class="kw">for</span> i in <span class="dt">range</span>(<span class="dt">len</span>(<span class="ot">self</span>.P)):
            <span class="ot">self</span>.assertEqual(<span class="dt">sum</span>(<span class="ot">self</span>.P[i, :]), <span class="dv">1</span>)

    <span class="kw">def</span> test_sum_one(<span class="ot">self</span>):
        <span class="ot">self</span>.assertTrue(np.allclose(<span class="dt">sum</span>(<span class="ot">self</span>.v), <span class="dv">1</span>, atol=<span class="ot">self</span>.TOL))

    <span class="kw">def</span> test_nonnegative(<span class="ot">self</span>):
        <span class="ot">self</span>.assertEqual(np.prod(<span class="ot">self</span>.v &gt;= <span class="dv">0</span>-<span class="ot">self</span>.TOL), <span class="dv">1</span>)

    <span class="kw">def</span> test_left_eigen_vec(<span class="ot">self</span>):
        <span class="ot">self</span>.assertTrue(np.allclose(np.dot(<span class="ot">self</span>.v, <span class="ot">self</span>.P), <span class="ot">self</span>.v, atol=<span class="ot">self</span>.TOL))

    <span class="kw">def</span> tearDown(<span class="ot">self</span>):
        <span class="kw">pass</span></code></pre>
<h3 id="using-nose-test-functions">Using <code>nose</code> test functions</h3>
<p>This example can also be written as nose <code>test_</code> functions. The required setup can be done in a <code>setup_func()</code> and then importing a <code>with_setup</code> decorator from <code>nose.tools</code>. This decorator will then run the setup function before every test is performed. Nose also allows you to specify <code>teardown_</code> functions as a second argument to <code>with_setup</code>.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="ch">from</span> nose.tools <span class="ch">import</span> with_setup

N = <span class="dv">27</span>
epsilon = <span class="fl">1e-2</span>
p = <span class="dv">1</span>/<span class="dv">3</span>
TOL = <span class="fl">1e-2</span>

<span class="kw">def</span> setup_func():
    <span class="co">&#39;&#39;&#39;</span>
<span class="co">        Setup a KMRMarkovMatrix and Compute Stationary Values</span>
<span class="co">    &#39;&#39;&#39;</span>
    <span class="kw">global</span> P                                            <span class="co"># Not Usually Recommended</span>
    P = KMRMarkovMatrixSequential(N, p, epsilon)
    <span class="kw">global</span> v                                            <span class="co"># Not Usually Recommended</span>
    v = mc_compute_stationary(P)

<span class="ot">@with_setup</span>(setup_func)
<span class="kw">def</span> test_markov_matrix():
    <span class="kw">for</span> i in <span class="dt">range</span>(<span class="dt">len</span>(P)):
        <span class="kw">assert</span> <span class="dt">sum</span>(P[i, :]) == <span class="dv">1</span>, <span class="st">&quot;sum(P[i,:]) </span><span class="ot">%s</span><span class="st"> != 1&quot;</span> % <span class="dt">sum</span>(P[i, :])

<span class="ot">@with_setup</span>(setup_func)
<span class="kw">def</span> test_sum_one():
    <span class="kw">assert</span> np.allclose(<span class="dt">sum</span>(v), <span class="dv">1</span>, atol=TOL) == <span class="ot">True</span>, <span class="st">&quot;np.allclose(sum(v), 1, atol=</span><span class="ot">%s</span><span class="st">) != True&quot;</span> % TOL

<span class="ot">@with_setup</span>(setup_func)
<span class="kw">def</span> test_nonnegative():
    <span class="kw">assert</span> np.prod(v &gt;= <span class="dv">0</span>-TOL) == <span class="dv">1</span>, <span class="st">&quot;np.prod(v &gt;= 0-TOL) </span><span class="ot">%s</span><span class="st"> != 1&quot;</span> % np.prod(v &gt;= <span class="dv">0</span>-TOL)

<span class="ot">@with_setup</span>(setup_func)
<span class="kw">def</span> test_left_eigen_vec():
    <span class="kw">assert</span> np.allclose(np.dot(v, P), v, atol=TOL) == <span class="ot">True</span>, <span class="st">&quot;np.allclose(np.dot(v, P), v, atol=</span><span class="ot">%s</span><span class="st">) != True&quot;</span> % TOL</code></pre>
<h3 id="using-nose-class-based-structures">Using <code>nose</code> class based structures</h3>
<p>Nose can also parse classes. As discussed in the <code>unittest</code> section in more complex test suites classes are useful for bringing structure to the code. While it is not a requirement to use <code>unittest.TestCase</code> in QuantEcon if you do choose to write tests in a class structure it can be helpful for cross readership to adopt the standard <code>setUp()</code> and <code>tearDown()</code> methods as used in <code>unittest.TestCase</code>. The main benefit of using Class structures is to collect your tests into one logical space and allow easy parameter passing without resorting to <code>global</code> variables etc.</p>
<pre class="sourceCode python"><code class="sourceCode python"><span class="kw">class</span> TestMcComputeStationaryKMRMarkovMatrix():
    <span class="co">&#39;&#39;&#39;</span>
<span class="co">        Test Suite for mc_compute_stationary using KMR Markov Matrix [suitable for nose]</span>
<span class="co">    &#39;&#39;&#39;</span>
    <span class="co"># Starting Values #</span>

    N = <span class="dv">27</span>
    epsilon = <span class="fl">1e-2</span>
    p = <span class="dv">1</span>/<span class="dv">3</span>
    TOL = <span class="fl">1e-2</span>

    <span class="kw">def</span> setUp(<span class="ot">self</span>):
        <span class="co">&#39;&#39;&#39;</span>
<span class="co">            Setup a KMRMarkovMatrix and Compute Stationary Values</span>
<span class="co">        &#39;&#39;&#39;</span>
        <span class="ot">self</span>.P = KMRMarkovMatrixSequential(<span class="ot">self</span>.N, <span class="ot">self</span>.p, <span class="ot">self</span>.epsilon)
        <span class="ot">self</span>.v = mc_compute_stationary(<span class="ot">self</span>.P)

    <span class="kw">def</span> test_markov_matrix(<span class="ot">self</span>):
        <span class="kw">for</span> i in <span class="dt">range</span>(<span class="dt">len</span>(<span class="ot">self</span>.P)):
            <span class="kw">assert</span> <span class="dt">sum</span>(<span class="ot">self</span>.P[i, :]) == <span class="dv">1</span>, <span class="st">&quot;sum(P[i,:]) </span><span class="ot">%s</span><span class="st"> != 1&quot;</span> % <span class="dt">sum</span>(<span class="ot">self</span>.P[i, :])

    <span class="kw">def</span> test_sum_one(<span class="ot">self</span>):
        <span class="kw">assert</span> np.allclose(<span class="dt">sum</span>(<span class="ot">self</span>.v), <span class="dv">1</span>, atol=<span class="ot">self</span>.TOL) == <span class="ot">True</span>, <span class="st">&quot;np.allclose(sum(v), 1, atol=</span><span class="ot">%s</span><span class="st">) != True&quot;</span> % <span class="ot">self</span>.TOL

    <span class="kw">def</span> test_nonnegative(<span class="ot">self</span>):
        <span class="kw">assert</span> np.prod(<span class="ot">self</span>.v &gt;= <span class="dv">0</span>-<span class="ot">self</span>.TOL) == <span class="dv">1</span>, <span class="st">&quot;np.prod(v &gt;= 0-TOL) </span><span class="ot">%s</span><span class="st"> != 1&quot;</span> % np.prod(<span class="ot">self</span>.v &gt;= <span class="dv">0</span>-<span class="ot">self</span>.TOL)

    <span class="kw">def</span> test_left_eigen_vec(<span class="ot">self</span>):
        <span class="kw">assert</span> np.allclose(np.dot(<span class="ot">self</span>.v, <span class="ot">self</span>.P), <span class="ot">self</span>.v, atol=<span class="ot">self</span>.TOL) == <span class="ot">True</span>, <span class="st">&quot;np.allclose(np.dot(v, P), v, atol=</span><span class="ot">%s</span><span class="st">) != True&quot;</span> % <span class="ot">self</span>.TOL</code></pre>
<h2 id="references">References</h2>
<ol style="list-style-type: decimal">
<li><a href="https://nose.readthedocs.org/en/latest/">Nose Documentation</a></li>
<li><a href="https://docs.python.org/2/library/unittest.html">Unittest Documentation</a></li>
<li><a href="https://nose.readthedocs.org/en/latest/writing_tests.html">Writing Tests with Nose</a></li>
</ol>

<?php
    include ('footer.php');
?>