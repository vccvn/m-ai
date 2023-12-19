<?php

?>


<div class="custom-background theme-yoast-theme woocommerce-no-js has_paypal_express_checkout theme-home theme-academy">
	<div class="site">



		<main role="main">

			<article class="row">
				<section class="content">

					<div id="input" class="form-container">
						<div id="inputForm" class="inputForm">
							{{-- <label for="content">Enter the content you'd like to analyse</label> --}}
							<div id="wp-content-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
								<link rel='stylesheet' id='dashicons-css' href='../../wp/wp-includes/css/dashicons.min271b.css?ver=5.9' type='text/css' media='all' />
								<link rel='stylesheet' id='editor-buttons-css' href='../../wp/wp-includes/css/editor.min271b.css?ver=5.9' type='text/css' media='all' />
								<div id="wp-content-editor-tools" class="wp-editor-tools hide-if-no-js">
									<div class="wp-editor-tabs"><button type="button" id="content-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="content">Visual</button>
										<button type="button" id="content-html" class="wp-switch-editor switch-html" data-wp-editor-id="content">Text</button>
									</div>
								</div>
								<div id="wp-content-editor-container" class="wp-editor-container">
									<div id="qt_content_toolbar" class="quicktags-toolbar hide-if-no-js"></div><textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="content" id="content"></textarea>
								</div>
							</div>

							<label for="focusKeyword">Focus keyword</label>
							<input type="text" id="focusKeyword" name="focusKeyword" placeholder="Choose a focus keyword" />
						</div>
						<div id="snippetForm" class="snippetForm">
							<label>Snippet Preview</label>
							<div id="snippet" class="output">

							</div>
						</div>
					</div>
					<div id="output-container" class="output-container">
						<p>This is what your page might look like on a Google search result page.</p>

						<p>Edit your SEO title and meta description by clicking the title and meta description!</p>

						<div id="output" class="output">

						</div>

						<h2>Content assessments</h2>
						<div id="contentOutput" class="contentOutput">

						</div>
					</div>
				</section>


			</article>


		</main>


	</div>



</div>
