# Category Landing Page

Use the Gutenberg block editor to add content to the top of your category archives, turning your category pages into rich, engaging, SEO friendly landing pages.

Once installed and set up, go to Landing Pages > Add New. Add any number of blocks and content to your landing page.  Look for the "Appears On" box in the right column. Select the taxonomy (ex: "Category") and the taxonomy term (ex: "Appetizers") that you would like this landing page used on. Click "Publish" and your landing page content will appear at the top of the archive.

## Screenshots
![backend](https://p198.p4.n0.cdn.getcloudapp.com/items/GGu0RdK1/Screen+Shot+2019-10-17+at+9.44.09+AM.png?v=f208287ed2315888ac1c8047369ea3ae)
Backend Editor

![frontend](https://p198.p4.n0.cdn.getcloudapp.com/items/GGu0Rdyp/Screen+Shot+2019-10-17+at+9.45.45+AM.png?v=a9504ad4c04ed7aa90647dbb26d2f780)
Frontend

## Setup

You'll need to do a bit of coding to get the landing page content to appear on your category archive pages. Every theme is different so I recommend you talk to your theme developer for guidance on how to add this.

Add the following code where you would like the landing page content to appear:

```
if( function_exists( 'category_landing_page' ) )
	category_landing_page()->show();
```

If you're using the [Genesis theme framework](https://www.billerickson.net/go/genesis/), you can add the following code to your theme's functions.php file to display the landing page content before the post listing but after the page title:

```
add_action( 'genesis_before_loop', 'be_show_category_landing_content' );
function be_show_category_landing_content() {
	if( function_exists( 'category_landing_page' ) )
		category_landing_page()->show();
}
```

### Customization
The plugin supports the `category` taxonomy by default, but you can add, remove, and change supported taxonomies using the `category_landing_page_taxonomies`.

If you wanted to turn your tag archives into landing pages as well, add the following code to your theme's functions.php file:

```
add_filter( 'category_landing_page_taxonomies', 'be_landing_page_taxonomies' );
function be_landing_page_taxonomies( $taxonomies ) {
	$taxonomies[] = 'post_tag';
	return $taxonomies;
}
```
