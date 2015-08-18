=== Chamber Dashboard Business Directory ===
Contributors: gwendydd
Tags: Chamber of Commerce, business directory, businesses
Donate link: http://chamberdashboard.com/donate
Requires at least: 3.7
Tested up to: 4.3
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create a directory of businesses.  Specifically designed for chambers of commerce to store information about businesses and display them on a website.

== Description ==
Chamber Dashboard Business Directory is a part of the Chamber Dashboard collection of plugins designed to meet the needs of chambers of commerce.

= With Chamber Dashboard Business Directory, you can: =
*   create a database of the businesses in your organization
*   display a business directory on your website
*   customize what information is displayed about the businesses
*   export a CSV of the business in your directory


You can learn more at [chamberdashboard.com](http://chamberdashboard.com)

For full instructions about how to use the plugin, go to [Chamber Dashboard Documentation](http://chamberdashboard.com/support/documentation)

= Other Chamber Dashboard Plugins =
* [Chamber Dashboard Events Calendar](https://wordpress.org/plugins/chamber-dashboard-events-calendar/) - Display a calendar of your organization's events
* [Chamber Dashboard CRM](https://wordpress.org/plugins/chamber-dashboard-crm/) - Keep track of the people associated with your organization and their activities
* [Chamber Dashboard Member Manager](https://wordpress.org/plugins/chamber-dashboard-member-manager/) - Track membership levels and benefits, collect membership payments online

= Contributors = 
Several people have contributed to the development of Chamber Dashboard:
* Jonathan Kay, jpkay
* Mike Davis, jmdxsvhs15 - map clustering
* Justin Ribeiro - business categories shortcode



== Installation ==
= Using The WordPress Dashboard =

1. Navigate to the \'Add New\' in the plugins dashboard
2. Search for \'chamber dashboard business directory\'
3. Click \'Install Now\'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the \'Add New\' in the plugins dashboard
2. Navigate to the \'Upload\' area
3. Select `chamber-dashboard-business-directory.zip` from your computer
4. Click \'Install Now\'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `chamber-dashboard-business-directory.zip`
2. Extract the `chamber-dashboard-business-directory` directory to your computer
3. Upload the `chamber-dashboard-business-directory` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==
= How do I display the business directory on my site? =
Create a page, and insert the following shortcode:
[business_directory]

There are lots of options for this shortcode.  For a full description, see [Chamber Dashboard Documentation](http://chamberdashboard.com/document/displaying-business-directory-site/)

= Will it work with my theme? =
Probably!  It is designed to work with any theme that follows basic WordPress coding practices. 

= Can I see other sites that are using Chamber Dashboard? = 
Yes!  We have a [map of organizations using Chamber Dashboard](https://chamberdashboard.com/chamber-dashboard-user-map/) 

= I want the plugin to do _____.  Can you make it do that? =
Probably!  I am definitely interested in making this as useful as possible for chambers of commerce, so please let me know what features you need!  You can use the contact form at [chamberdashboard.com/contact](http://chamberdashboard.com/contact)

= Is it translation-ready? =
Yes, the plugin is ready to be translated!  .po and .mo files are included.  If you translate the plugin, I would love to include your translation with future releases of the plugin.



== Screenshots ==
1. Entering business information

== Changelog ==
= 2.6.2 =
* fixed bug in Featured Business widget

= 2.6.1 =
* changed H2s to H1s in admin to conform to new accessibility guidelines
* added "Add-Ons" page

= 2.6 =
* added "website" to list of social media icons
* added Featured Business widget
* added country field to address for better geolocation

= 2.5.4 =
* In export, if you haven't selected a phone type or email type, the parenthesis will not appear
* moved address display to a separate function
* added filters to all content functions to facilitate customization

= 2.5.3 =
* fixed pagination on business search results shortcode
* added ability to delete custom fields
* added filter to map so that you can customize map colors

= 2.5.2 =
* closed quotes on custom field class names

= 2.5.1 =
* fixed bug that caused infinite loop on business_directory shortcode if you do not have CRM installed

= 2.5 =
* added CSS classes to custom field labels
* added private category to business
* added filters to display CRM contact information

= 2.4.3 =
* added CSS classes to custom fields
* fixed bug that prevented featured image from displaying on single business
* fixed bug that prevented javascript functions from running on new locations
* remove title li from business categories shortcode and widget
* fixed showcount in business categories widget

= 2.4.2 =
* added business category widget

= 2.4.1 =
* further updates to maps to avoid Google API quotas

= 2.4 =
* rewrote maps to make them reliable
* add clustering option to map shortcode, thanks to Mike Davis, jmdxsvhs15
* add option to change ordering of taxonomy archives
* add option to allow comments on single business view
* add parameters to business categories shortcode
* add button to let users copy a location's information to the business contact 
* fixed bug that caused some custom fields to not display

= 2.3.8 =
* fixed bug that caused some custom fields to not display
* changes to language files to facilitate translations
* fixed Google maps grey box problem

= 2.3.7 =
* fixed custom field error message
* potential fix for Google maps grey box problem

= 2.3.6 =
* fixed bug in business search results shortcode
* fixed PHP error on single business view

= 2.3.5 =
* fixed ampersands in map info windows
* made maps on single business view work on hosts that don't support fopen

= 2.3.4 =
* fixed bug that prevented membership levels from displaying on single business view
* fixed bug that prevented some custom fields from displaying
* added Instagram to social media options

= 2.3.3 =
* more thoroughly fixed bug in [business_search] shortcode
* made maps work on hosts that don't support fopen

= 2.3.2 =
* made featured image link to single business page in shortcode and search results
* fixed bug in [business_search] shortcode

= 2.3.1 =
* added Trip Advisor and Urban Spoon to social media options
* on taxonomy view, featured image links to single business page

= 2.3 =
* fixed bug on search results shortcode
* added Yelp to social media options
* prevented empty fields from displaying
* minor visual improvements

= 2.2.1 = 
* added "membership status" parameter to business directory shortcode

= 2.2 =
* increased compatibility with other Chamber Dashboard plugins

= 2.1 =
* check URLs for "http://" to make sure links work
* updated welcome page
* add member manager information to sidebar when editing businesses
* fixed bug that was preventing custom fields from displaying

= 2.0.1 =
* minor fix to improve plugin compatibility

= 2.0 = 
* added billing contact information to work with Member Manager plugin
* minor bug fixes

= 1.9.1 =
* made sure social media icons are included in plugin

= 1.9 =
* added social media fields
* fixed bug that prevented custom fields from displaying
* fixed display errors when business has empty fields
* made ampersands display correctly on map

= 1.8.1 =
* fixed minor bug when displaying email addresses in business_directory shortcode

= 1.8 =
* added search_form and search_results shortcodes
* added search widget
* minor fixes and code clean-up

= 1.7.3 =
* fixed map issues

= 1.7.2 =
* lots of bug fixes

= 1.7.1 =
* fixed issues with labels echoing in the wrong place
* fixed typo that broke search form

= 1.7 =
* fixed typos
* added category shortcode thanks to Justin Ribeiro https://github.com/justinribeiro/chamber-dashboard-business-directory/tree/add-category-shortcode

= 1.6.11 =
* made even more strings translatable

= 1.6.10 =
* made more strings translatable

= 1.6.9 =
* minor bug fixes
* added welcome page

= 1.6.8 =
* minor bug fixes

= 1.6.7 =
* fixed JavaScript error on grid layout

= 1.6.6 =
* fixed a few more URL bugs
* added phone number to business overview page

= 1.6.5 =
* fixed icon URL

= 1.6.4 =
* fixed bug with map on single business view

= 1.6.3 =
* made Business Directory compatible with new Chamber Dashboard CRM plugin

= 1.6.2 =
* Small bug fixes

= 1.6.1 = 
* Cleaned up some debugging code

= 1.6 = 
* Added ability to import business from a CSV

= 1.5 = 
* Fixed a bunch of debug errors
* Added export capability

= 1.4 =
* Fixed bug where locations with "do not display" appeared on map anyway
* Added business search form
* other small fixes

= 1.3 = 
* Created filters to allow use of theme archive pages for business category and membership level
* Other small fixes

= 1.2 =
* Added the ability to create custom fields for businesses
* Bug fixes

= 1.1 =
* Added Google map to single business view
* Added shortcode to create map of all businesses
* Added category and membership level to body and post classes
* Bug fixes

= 1.0 =
* First release
