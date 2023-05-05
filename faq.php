<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <main id="faq">
	<h1>Frequently Asked Questions</h1>
	<section class="question">
		<h2>What is Lorem Ipsum?</h2>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc suscipit faucibus nisi, in ultrices nulla feugiat vel.</p>
	</section>
	<section class="question">
		<h2>How can I contact customer support?</h2>
		<p>You can contact customer support by emailing us at support@website.com or by calling our toll-free number at 1-800-123-4567.</p>
	</section>	
	<section class="question">
		<h2>Is there a money-back guarantee?</h2>
		<p>Yes, we offer a 30-day money-back guarantee on all of our products.</p>
	</section>	
	<section class="question">
		<h2>Do you offer free shipping?</h2>
		<p>Yes, we offer free shipping on all orders over $50.</p>
	</section>
    <section class="question">
		<h2>Is it better to call Saul?</h2>
		<p>Yes...yes it is.</p>
	</section>
  <?php outputFooter() ?>
</body>
</html>
