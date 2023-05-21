<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <h1 id="faq-header">Frequently Asked Questions</h1>
  <main id="faq">
	<section class="question">
		<section class="question-head">
			<h2>What is Lorem Ipsum?</h2>
			<button class="dropdown-button" aria-expanded="false">
				<span class="down-button"><i class='far fa-caret-square-down'></i></span>
			</button>
		</section>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc suscipit faucibus nisi, in ultrices nulla feugiat vel.</p>
	</section>
	<section class="question">
		<section class="question-head">
			<h2>How can I contact customer support?</h2>
			<button class="dropdown-button" aria-expanded="false">
				<span class="down-button"><i class='far fa-caret-square-down'></i></span>
			</button>
		</section>
		<p>You can contact customer support by emailing us at support@website.com or by calling our toll-free number at 1-800-123-4567.</p>
	</section>	
	<section class="question">
		<section class="question-head">
			<h2>Is there a money-back guarantee?</h2>
			<button class="dropdown-button" aria-expanded="false">
				<span class="down-button"><i class='far fa-caret-square-down'></i></span>
			</button>
		</section>
		<p>Yes, we offer a 30-day money-back guarantee on all of our products.</p>
	</section>	
	<section class="question">
		<section class="question-head">
			<h2>Do you offer free shipping?</h2>
			<button class="dropdown-button" aria-expanded="false">
				<span class="down-button"><i class='far fa-caret-square-down'></i></span>
			</button>
		</section>
		<p>Yes, we offer free shipping on all orders over $50.</p>
	</section>
    <section class="question">
		<section class="question-head">
			<h2>Is it better to call Saul?</h2>
			<button class="dropdown-button" aria-expanded="false">
				<span class="down-button"><i class='far fa-caret-square-down'></i></span>
			</button>
		</section>
		<p>Yes...yes it is.</p>
	</section>
  </main>
  <?php outputFooter() ?>
</body>
</html>