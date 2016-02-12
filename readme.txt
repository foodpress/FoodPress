=== FoodPress ===
Contributors: Ashan Jay & Michael Gamble
Plugin Name: FoodPress
Author URI: http://www.myfoodpress.com/
Tags: restaurants, menu, food, eating, menu management
Requires at least: 3.8
Tested up to: 4.4.2
Stable tag: 1.3.2

== Description ==
FoodPress is a complete restaurant menu management system that allow you to create various menus with categorizations and more.

== Installation ==
1. Unzip the download zip file
1. Upload `foodpress` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 1.3.5 (2016-) =
FIXED: Plugin name passed incorrect in gihub updater for new update infor window

= 1.3.4.1 (2016-2-12)
FIXED: Reservation input field color not saving in backend
FIXED: Reservation success message checkmark not displaying icon

= 1.3.4 (2016-2-12) =
FIXED: Errors occuring when viewing details of updates

= 1.3.3 (2016-2-12) =
FIXED: Dish type not working with old shortcode
FIXED: Errors occuring after auto-updating

= 1.3.2 (2016-2-10) =
ADDED: Styles for Menu Descriptions for Headings, Lists, etc. within WYSIWYG editor
ADDED: github based auto updates for the plugin
ADDED: International phone number validation and required field option
ADDED: Jquery trigger menu_lightbox_open when lightbox complete loading
FIXED: Dish type shortcode not working
FIXED: Meal type header coming empty for locations when empty meal types
FIXED: Dish type var incorrect causing specific type menu not work
FIXED: unnecessary shortcode options showing incorrectly
FIXED: Party size placeholder text missing in translations
FIXED: Start reservations from tomorrow not working
FIXED: Mobile lightbox menu not closing
FIXED Addons and licenses page not showing all addon information 
FIXED: OrderOnline select options to open lightbox when ux none for menu
FIXED: Missing arrows.png image wrong url in styles
UPDATED: Shortcode generator to prevent default <a> click actions
UPDATED: Duplication of menu items functions
UPDATED: Made the lightbox close button bigger for smaller screen 

= 1.3.1 (2016-1-20) =
FIXED: timepicker initiating on unsupported pages
FIXED: lightbox menu make page stop scrolling
FIXED: Menu type ss_5 not working properly

= 1.3 (2016-1-7) =
ADDED: Language Options for Date and Phone Number Placeholders
ADDED: Focused tab variable for tabbed menu
ADDED: Support for specific meal and dish type menu
ADDED: scroll to meal types menu type
ADDED: Ability to reorder and select/deselect menu card rows of data
ADDED: Option to show menu last updated date
ADDED: New spice level display style
ADDED: Phone number as optional required field
ADDED: Unreservable dates for reservation form
ADDED: Ability to redirect reservation form after submission
ADDED: Ability to set restaurant locations in reservation form
FIXED: meal type description not showing on tabbed version
FIXED: compatibility with eventon
FIXED: Reservation form AM/PM for 12:00
FIXED: Safari Browser Time Field Issue
FIXED: Responsive Styles for Price and Menu Item Title on Mobile Devices
FIXED: Meal type not showing on lightbox menu item
FIXED: Empty tabs not hiding from the menu
FIXED: Sub categorizing with dish type breaking menu
FIXED: appearance reseting after update
FIXED: Meal type description not showing under tabbed menu
FIXED: collapsable dish type not functioning
FIXED: Translation issues on menu items
FIXED: ux value for menu none not working
FIXED: Dish type showing ID column incorrectly
FIXED: Reservation submit button appearance not working
FIXED: Party size interger value validation
UPDATED: Restructure the shortcode generator easier shortcode variables
UPDATED: Menu generation code have been completly redone to improve speed
UPDAETD: Reservation form to slide to top of page after submission
UPDATED: Reservation form - party size default value to 1
UPDATED: Compatibility with wordpress 4.4

= 1.2.4 (2015-8-25) =
ADDED: mealtype descriptions added to tabbed menu version
ADDED: time and date fields as dropdown fields
ADDED: ability to make fields required in reservation form
FIXED: thumbnails for all versions of menu to use image instead of background
FIXED: mealtype description only work on first tabbed item
FIXED: date format to be changed dynamically
FIXED: WPML issues
FIXED: meal type description only working on first tab
FIXED: apostrophe in meal type name not showing correct in tabbed menu
FIXED: reservation form: date format to match WP date format
FIXED: Reservation form:  field placeholder appearance
FIXED: Compatibility with WP 4.3 version
FIXED: License activation not working
UPDATED: dynamic styles to be updated upon new version update

= 1.2.3 (2015-4-8) = 
ADDED: Reservation form to be able to link to a page
FIXED: Tooltip display issue
FIXED: Featured items breaking tabbed menu
FIXED: Appearance settings for reservation form
FIXED: Style issues
UPDATED: AJDE backend settings function

= 1.2.2 (2015-3-23) = 
ADDED: phone number to emails
FIXED: phone number as required field in the form
FIXED: Deleting reservations from all reservations page
FIXED: email address not saving on manual reservations
FIXED: Reservation form not saving data correctly 
FIXED: Placeholder color for input fields in reservation form
FIXED: Confirm reservations from draft bug
FIXED: Widget not working correct
UPDATED: Support tab links

= 1.2.1 (2015-3-16) =
FIXED: Dish type categorization not working

= 1.2 (2015-3-15)
ADDED: in page reservation form
ADDED: time slot increment options for reservation form
ADDED: Multiline field for reservation form
ADDED: Support for 5 additional reservation form fields with plugin hooks
ADDED: Language tranalation for meal and dish types
ADDED: Complete language support for reservation emails
ADDED: Reservation email preview section to settings
ADDED: Dish type description for menus
ADDED: Ability to set reservation date format same as WP date format
ADDED: Reservation form validation before submission
ADDED: Dashboard reservation widget
ADDED: More reservation form appearance customization options
ADDED: Ability to block today reservations
FIXED: Backend admin translations
FIXED: Dish type and meal type menus with no term values not to work
FIXED: popup menu header styles when no menu image
FIXED: reservation form end time not restricting to limits
FIXED: Color picker to have a more visible select button
FIXED: Language variations not working for reservation form & front-end
FIXED: slashes on apostrophe on menu card and inside
FIXED: Reservations archive page to redirect to home page and not show all reservations
FIXED: Menu archive page not working for some
FIXED: Reservation confirmation email to have custom field details
UPDATED: Menu icon symbols section

= 1.1.10 (2014-11-29)
ADDED: Ability to add custom fields to reservation form
ADDED: Ability to set start and end time for reservation form
ADDED: Disable content filter for menu item text
ADDED: Ability to remove icon from meal type
ADDED: Capacity limit to reservation form party size
ADDED: Dish type collapsed on load
ADDED: Reset appearance colors to default option
ADDED: Dish type icons
FIXED: Menu description not formating in menu items
FIXED: Wordcount not working
FIXED: Reservations shortcode showing on top of the page
FIXED: Wordcount not working correct when short menu description present
FIXED: Icons fields in menu items post to hide when icons are not set in settings
UPDATED: Reservation form functionality
UPDATED: Reservation form language text

= 1.1.9 (2014-9-2)
FIXED: Higher res image for boxed category style menu
FIXED: Shortcode generator button for 3rd party plugin compatibility
FIXED: Error on box category menu pages
FIXED: Setting field names with aphostrophe not saving correct
FIXED: WYSIWYG shortcode button title text
UPDATED: Compatibility with Visual Composer third party plugin
UPDATED: Reservation form phone number validation updated for uk numbers

= 1.1.8(2014-8-20)
ADDED: 24 hour time support for time slots in reservation form
ADDED: Time restrictions for reservation form time slots
ADDED: Custom font family addition to menus via settings
ADDED: Dynamically generated styles box to styles tab
UPDATED: Languages tab with better UI and easier functionality
FIXED: Minor Menu card bugs
FIXED: Box category menu with multiple word category names breaking menu
FIXED: Write dynamic styles to header in settings not working
FIXED: Addon page mispresenting installed addon info
FIXED: 50% width featured items column not showing correct

= 1.1.7 (2014-7-31)
ADDED: WPML compatibility
FIXED: box menu responsive design
FIXED: featured images not working on menu items
FIXED: Subcategories inside boxed category menu
UPDATED: i18n translation text update with updated .po files

= 1.1.6(2014-7-26)
ADDED: Seperate menu locations category types and be able to show menus for locations
ADDED: The ability to write dynamic styles into header of the page
ADDED: Ability to collapse dish type category headers
ADDED: New categorized menu style utilizing icons
ADDED: Upto 3 custom language text support
ADDED: Reservation form and backend settings
ADDED: New font icon based Menu items icon for the left menu
FIXED: License activation not working
FIXED: SPicy level front end text translation
FIXED: Individual menu item styles not correctly showing up
UPDATED: Shortcode generator to assist new categorized menu options
UPDATED: Menu item edit page UI styles
UPDATED: Shortcode generator to be set height with scrollbars inside lightbox


= 1.1.5 (2014-5-6)
ADDED: menu item box hover color to appearances section
ADDED: the ability to assign icons next to meal type headers
ADDED: Variety of new icons to choose for menu items
ADDED: IDs to menu item category tags page
ADDED: Menu item ID on menu item edit page
ADDED: Quick edit for menu items now support additional fields
ADDED: Subheader field for menu items
ADDED: additional text field for menu items
ADDED: tabbed menu for categorized menu as a style of menu
UPDATED: mobile menu styles
FIXED: WordPress 3.9 compatibility
FIXED: shortcode generator minor bugs
FIXED: 3rd menu type category issue
FIXED: foodpress menu item URLs to support SSL

= 1.1.4 (2014-2-24)
ADDED: You can now add meal type description and via shortcode generator, select to show this under meal type name
ADDED: shortcode option to disable menu item clicks and read more button
ADDED: the ability to collapse menus on page load
ADDED: the ability to either have wysiwyg editor or single text line for custom meta fields
ADDED: one more custom meta field for menu items
ADDED: ability to category by dish type for meal type menu with a meal type ID
CHANGED: faster dynamic CSS load method - need re-saving appearnace
FIXED: foodpress settings page scripts and styles to eneuque to header
FIXED: popup price box color to match other price box colors
FIXED: CSS styles for mobile view for menu with images
FIXED: Bulk edit deleting all custom meta values for menu item

= 1.1.3 [2014-1-16]
UPDATED: compatibility with future addons
ADDED: Price column to all menu items admin page
ADDED: Easy icon picker for menu card icons
ADDED: Custom meta field icons to be able picked via icon picker
FIXED: Style issues
FIXED: Popup background loaded to show before running AJAX load
FIXED: Scrollbar click when popup open to not close popup

= 1.1.2 [2014-1-14]
UPDATED: Compatibility with future addons
FIXED: WP 3.5.1 compatibility
FIXED: Popup price box color can be changed from appearance now

= 1.1.1 [2014-1-6]
UPDATED: Color selection UI in foodpress settings appearance
ADDED: Automatic updated to foodpress plugin from your website wp-admin with activated licenses
ADDED: Menu Item widget. You can add individual items or execute foodpress shortcodes in the sidebar widget
FIXED: Vegetarian icon styles 
FIXED: nutrition information dissappearing after saved
FIXED: custom menu item field title name

= 1.1 [2014-1-2]
FIXED: CSS for spicy level icon
FIXED: color changes not reflecting on front end

= 1.0 [2013-12-31] =
* Initial release