# Condolence manager
The condolence manager is a plugin that every funeral director needs, enriching their website by adding an online obituary.

## Table of contents
- [Condolence manager](#condolence-manager)
  * [Table of contents](#table-of-contents)
  * [Description](#description)
  * [Features](#features)
    + [Livestream](#livestream)
    + [Overview of all obituaries](#overview-of-all-obituaries)
    + [Add obituaries through the condolences custom post](#add-obituaries-through-the-condolences-custom-post)
    + [Privately viewable condolence messages](#privately-viewable-condolence-messages)
    + [Flower arrangement order form](#flower-arrangement-order-form)
    + [Register for the post-funeral reception](#register-for-the-post-funeral-reception)
  * [Installation](#installation)
  * [Customization](#customization)
    + [Action hooks](#action-hooks)
    + [Filter hooks](#filter-hooks)
    + [Shortcodes](#shortcodes)

## Description
Funeral directors using the plugin on their WordPress website can create custom posts showing the deceased's information, picture and obituary. Web visitors can send messages of sympathy to the family of the deceased, order flowers and register to attend the post-funeral reception.

To prevent exposing the grieving relatives to inappropriate comments, the website administrator has to approve every condolence message.

The family will be informed of approved messages, as well as post-funeral reception registrations by email.

## Features
* Livestream
* Overview of all obituaries
* Add obituaries through the condolences custom post type
* Privately viewable condolence messages
* Flower arrangement order form
* Register for the post-funeral reception

### Livestream
Broadcast a funeral live by adding a live stream to a specific obituary when it takes place. That way people who aren’t able to physically attend the service can watch it online. Livestream videos from Youtube, Vimeo, or other sources can be embedded in the obituary page.

![Livestream screenshot](assets/img/livestream.png?raw=true "Livestream screenshot")

### Overview of all obituaries
The archive page displays all published obituaries.
On the single page, the details of the deceased person are visible.

### Add obituaries through the condolences custom post
* The administrator can add, view, modify and delete obituaries.
* Web visitors can show their support to relatives of the deceased beneath the obituary. The administrator needs to approve these condolence messages.
overzicht

![Overview screenshot](assets/img/overview.png?raw=true "Overview screenshot")

### Privately viewable condolence messages
* If a condolence message is approved, only the family or friends of the deceased can view this message on a page with a unique URL.
* The deceased’ loved ones can also comment on a condolence message. The comment will be sent by e-mail to the author of the condolence.

### Flower arrangement order form
For every obituary, a checkbox can be enabled to show the order form to purchase flowers. Visitors can select different flower arrangements below the obituary and set a message to be displayed on the funeral flowers’ ribbon.
![Flowershop screenshot](assets/img/flowershop.png?raw=true "Flowershop screenshot")

### Register for the post-funeral reception
Post-funeral receptions offer acquaintances, close friends and family members a chance to spend time together and share memories of the deceased in a more casual setting.
The administrator chooses to display a registration form below the obituary so people can let the family know whether or not they will be attending.
![Post-funeral reception screenshot](assets/img/post-funeral-reception.png?raw=true "Post-funeral reception screenshot")

By default, the form has the following fields:

* First name
* Surname
* Street
* Number
* Zip code
* City
* E-mail address
* Phone
* Presence funeral reception
* Number of people that will attend

## Installation
1. From the dashboard of your site, navigate to Plugins --> Add New.
1. Select the Upload option and hit \"Choose File.\"
1. When the popup appears select the condolatie-manager.zip file from your desktop.
1. When it\'s finished, activate the plugin.
1. Add condolences through the condolences custom post.

## Customization

### Action hooks
1. `cm_render_metabox` - use to add new field in backend on post site
1. `cm_backend_js` - use to add javascript on backend post site
1. `cm_form_field` - use to add extra content/field in coffie table form
1. `cm_handle_form` - use to handle submitted data on backend in controller

### Filter hooks
1. `cm_search_button_text` - use to change the search field button's text

### Shortcodes
1. `[cm_search]`: place this shortcode on any page to display a search field which can filter deceased by name or location.
