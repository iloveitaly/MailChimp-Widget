# WordPress MailChimp Subscription Widget

Simple widget for creating simple signup forms that drop into a MailChimp list. Original idea and code is based off of this [MailChimp-Widget repository](https://github.com/jameslafferty/MailChimp-Widget). I've since made a bunch of customizations centered around making the plugin more customizable both from the user and development perspective.

Here is a high level overview of the features:

* Multiple widgets on a single page (good for lead pages)
* Defaults to bootstrap classes
* Easy template customization
* Shortcode

## Customization

* Add `templates/mc-widget.php` to your theme to customize the widget template. Copy [the default template]() for an easy starting point.
* The shortcode has the following customization options:
  * learn_more - path for learn more button, can be empty
  * description - can be empty
  * before_widget
  * after_widget
  * after_title
  * signup_text
  * collect_last
  * collect_first

You can also setup custom forms. The `js-mailchimp-form` class will notify the JS library to hook into the `submit` event on the form.

```html
<form action="/blog/" class='js-mailchimp-form' method="post">
  <input type="hidden" name="ns_mc_number" value="2">
  <div class="input-group">
    <input class="form-control required email" type="email" name="ns_widget_mailchimp_email" placeholder="Email Address" value="" />
    <span class="input-group-btn"><button class="btn btn-primary" id="mc-embedded-subscribe" name="subscribe" type="submit">Subscribe</button></span>
  </div>
</form>
```

## TODO

* Use `data` attributes to trigger MC signup on any forms
* Select different templates in MC widget admin
* Better `GROUPINGS` merge var handling
* If group type is radio button, it's possible to 'subscribe' two separate times to two groups that were designed to be mutually exclusive
* Leaner JS. Remove spinner & mask dependencies
* Use grunt for JS + CSS minification / workflow
* More code cleanup, move widget admin into template instead of mixing the HTML in with the class code