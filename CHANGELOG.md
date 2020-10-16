# CHANGELOG

requires: 4.6
tested: 5.4.1

## Version 2.8.1 2020/10/16
- Update responsive styling

## Version 2.7.0 2020/09/22
- Added checkbox show funeraldate

## Version 2.6.2 2020/09/22
- fixed email subject & title in Products_Email_Controller

## Version 2.6.1 2020/09/21
- more compatability with older PHP versions in Products_Email_Controller

## Version 2.6.0 2020/09/21
- added order confirmation email to customer

## Version 2.5.9 2020/09/16
- Changed HTML structure of deceased template

## Version 2.5.8 2020/08/20
- Added pagination to the archive.

## Version 2.5.7 2020/08/12
- Added "disabled" state for condole button when post does not allow comments.

## Version 2.5.6 2020/07/13
- fixed conditional for determining the relation type of the deceased.


## Version 2.5.5 2020/07/10
- single and archive share new "deceased" template
- birth place / date on a single line
- death place / date on a single line

## Version 2.5.4 2020/07/09
- fixed plugin description

## Version 2.5.3 2020/07/09
- renamed conman_ hooks to cm_
- fixed error notices instead of showing relations in single and archive
- fixed crash on showing coffee table form
- added house number field for order form
- fixed html rendering bug 

## Version 2.5.2 2020/06/19
- merged 2.4.9 and 2.5.1 versions

## Version 2.5.1 2020/06/03
- improvement: redudant js removed, not used js removed
- improvement: single.php file links to buttons, to inherit css styling from theme

## Version 2.5.0 2020/06/03
- improvement: css cleaned for archive view
- improvement: archive.php file cleanup redudant code & logic, overall better html output

## Version 2.4.9 2020/06/02
- fix: added configurable order confirmation message
- fix: made address fields required in order form

## Version 2.4.8 2020/06/02
- fix: translation "opgebaard te:" > "Uitvaartcentrum:"
- fix: added more info to Order email + translations
- fix: fixed crash in single when funeraldate not set

## Version 2.4.7 2020/05/28
- fix: fixed save_order_metadata callback in Order_Type (email not being sent when making order as not logged in)

## Version 2.4.6 2020/05/27
- fix: added missing space in archive
- fix: fixed conditional check for livestream embed

## Version 2.4.5 2020/05/26
- fix: conditional rendering for dates in single and archive

## Version 2.4.4 2020/05/26
- show funeral date in archive and single

## Version 2.4.3 2020/05/26
- send email after order form submitted bug fix

## Version 2.4.2 2020/05/26
- Update Flower button by disabling it when the funeral date is too close
- Update dates to translate and use WordPress format

## Version 1.5.1 2020/04/23
- Fix assets Css
- Fix normalize dates


## Version 1.4.3 2019/04/24
- Fix display comment button single.php

## Version 1.4.2 2019/03/25
- Translation fix ( rouwbetuiging -> condoleer )

## Version 1.4.1 2019/01/29
- Added smooth scroll to single.php
- Update number of comments

## Version 1.4.0 2019/01/14
- New Updater
- Add shortcode for archive page
- Update deprecated create_function()
- Update templates for Enfold theme

## Version 1.3.3 2018/11/28
- Css for for Archive

## Version 1.3.2 2018/06/28
- REWORK FR translations.

## Version 1.3.1 2017/11/08
- ADDED tranlation for nl_BE.
- FIX bug in updater.

## Version 1.3 2017/11/07
- FIX translations.
- UPDATE buttons.

## Version 1.2.3 2017/09/20
- FIX bug in ajax-url.

## Version 1.2.2 2017/08/21
- ADDED tranlation for nl_BE.
- FIX bug in updater.

## Version 1.2.1 
- FIX permalink url's with WPML plugin.

## Version 1.2
- ADDED coffee table
- ADDED premium plugin access

## Version 1.1
- ADDED bulk script to change CPT slug

## Version 1.0
- ADDED CPT support
- ADDED custom fields for CPT
- ADDED Email to family
- ADDED Settings field to change front-end design
