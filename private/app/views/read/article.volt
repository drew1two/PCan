
<?php echo $this->getContent(); ?>

<div class="container">
    <style type="text/css">

        h1,h2 {
            text-align:center;
        }
        p {  
            margin-left: 3em;
            margin-right:3em;
        }
    </style>
    <h2>{{ blog.title }}</h2>
    <span  >{{ blog.article }}</span>
</div>

<p>To take part in the ParraCAN private community, view, or contribute  {{ link_to('session/signup', 'signup') }}
<nbsp/> or {{ link_to('session/login', 'login') }}</p><hr/>

<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = 'parracan-org'; // required: replace example with your forum shortname

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>




