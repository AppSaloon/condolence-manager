=== Condolence manager ===
Contributors: Appsaloon
Tags: Condolence, deceased, comment, reply, private, coffee table
Donate link: http://www.appsaloon.be
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 2.5.8
License: GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows visitors to write a message of condolence to the family of the deceased and register to attend the coffee table.

== Description ==
This plugin is made especially for funeral directors.
It\'s an online tool to give friends and family the opportunity to show their support to relatives of the deceased.

You can create a post with info and image about the  deseaded and add an obituary to this post.
You can determine which fields are displayed in the frontend and in what order.
Webvisotors can offer one\'s condolence on this obituary notice and can register to attend the coffee table.

The funeral director needs to approve the condolences before the family can read and comment. Registers are send by mail to the familie of the deceased.
The family is informed by mail when a new condolence is approved and comments on this condolence are send to the mailadres of the person who gave his condolence.
The plugin generates a unique url where only the family can view these condolences.

To overwrite the frontend template just place a copy of single.php and archive.php (from the templates directory) in your theme, taken into account our template Hierarchy (condolatie-manager-plugin/single.php)

== Installation ==
1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit \"Choose File.\"
3. When the popup appears select the condolatie-manager.zip file from your desktop.
4. When it\'s finished, activate the plugin.
5. From your site administration, click on Condolence manager to determine which fields are displayed and in what order and submit.
6. Add condolences through the condolences custom post.


== Screenshots ==
1. Condolences postfields - overview
2. Condolences postfields - create private url for the family to view the condolences and reply
3. Condolences postfields - add coffee table registration form to frond-end and add emailaddress to send registrations to
4. Condolence manager -  Determine which fields are displayed and in what order

== Hooks and filters ==
1. cm_render_metabox - use to add new field in backend on post site
2. cm_backend_js - use to add javascript on backend post site
3. cm_form_field - use to add extra content/field in coffie table form
4. cm_handle_form - use to handle submitted data on backend in controller


== Shortcodes ==
1. [condolence_overview]: shortcode to add an overview of all condolences to a page with parameters posts_per_page and pagination. Default posts_per_page are those set in your default wp settings and default pagination is set to false. To add pagination add pagination="true" to the shortcode.

== Credits ==
* Thanks to Jean François Dejonghe for the French translation.
* Thanks to Mark Creeten for the German translation.
