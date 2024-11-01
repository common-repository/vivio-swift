=== Vivio Swift ===
Contributors: viviotech
Tags: cache, faster, performance, optimize, accelerate
Donate link: https://code.viviotech.net/wp/vivio-swift/
Requires at least: 4.0
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 5.6
License: GPL3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

The Vivio Swift WordPress Accelerator (beta) improves your sites speed and load times by providing a comprehensive page caching system that completely bypasses PHP using .htaccess files. The primary goal of this realease is to demonstrate and get feedback on the page caching systems, exclusion system, logging features, and refresh events that are provided by Vivio Swift. Your constructive criticism is welcome and greatly appreciated!

== Description ==

The Vivio Swift WordPress Accelerator (beta) improves your sites speed and load times by providing a comprehensive page caching system that completely bypasses PHP using .htaccess files. The primary goal of this realease is to demonstrate and get feedback on the page caching systems, exclusion system, logging features, and refresh events that are provided by Vivio Swift.

= Page Caching =

* Highly aggressive default caching system works out-of-the-box on most installs
* Converts processing-heavy PHP pages to fast-loading HTML pages
* Dramatically reduces web server CPU and Memory use
* Dramatically reduces database server CPU and Memory use
* Completely bypasses PHP processing engine using extremely fast .htaccess redirects
* Dramatically improves existing CDN benefits by making more site pages cachable

= Browser Caching =

* Flexible Browser caching based on file types
* Group file types based on built-in catagories, like images, static assets, etc
* Ability to create your own file groups and configure caching to your specific needs

= Exclusion System =

* Highly Configurable exclusion system prevents caching to important requests
* Exclude requests based on cookies, or various path-based attributes

= Logging System =

* Comprehensive Logging System tells you exactly what's going on within Vivio Swift
* Distinct logging levels provides you with as much or as little information as you need.

= Refresh Events =

* Update your cache whenever certain events occur, like when you publish a new post.

= Remove Query Strings from Static Resources =

* Remove query strings from static WordPress resources to make them easier to cache

== Installation ==
1. Log in to your WordPress development site
1. Click Plugins->Add New in the menu on the left
1. Click the "Upload Plugin" button at the top of the page
1. Upload the vivio-swift.zip file
1. Activate the Vivio Swift Plugin.

The default options are designed to work with most sites, but you're welcome to explore and let us know if you have any questions at all.

= System Requirements =

Vivio Swift currently officially supports Apache. While other web servers may work, they are not officially supported.

Required Features
* .htaccess support

Required Apache Modules
* mod_rewrite
* mod_header (for browser caching support)


== Solutions to Common Problems ==

The Vivio Swift plugin requires clear abstraction bettween front-end code (HTML, JavaScript, and CSS) and back-end code (PHP) in order to provide a benefit to it's users. A great number of developers do not take the time to properly and clearly separate front-end code from back-end code in their development processes. While mixing front-end code with back-end coe is understandable from a "get the project done in the shortest amount of time possible" standpoint, it is generally considered to be bad coding practice by the established developer community for good reason.

Pages that mix front-end code and back-end code are going to be the cause of the vast majority of problems you will experience while working with Vivio Swift. If you're not a developer, the Vivio Swift plugin provides the tools you need to work around those pages by excluding them from the caching process. You will lose the speed benefit from caching that Vivio Swift provides on those pages, but you will still benefit by using Vivio Swift on the pages that don't have those issues. Check out the "Excludes" area in the Vivio Swift plugin to see what types of excludes you can set up for your site, or contact us if you would like to take advantage of Vivio's Extended Support service and we can set these rules up for you.

If you are a developer (particularly a theme developer) you can drastically decrease your users load time by separating out your front-end code from your back-end code. Specifically, use JavaScript to pull data from a WordPress web service instead of embedding PHP functions on the front-end of your theme or plugin. This will theme's or plugin's performance not only be much better with Vivio Swift, but with all caching layers (like CDN's). 

Caching support is a marketable feature! Boost your sales by letting your users know that your theme or plugin is fully compatible with caching plugins like Vivio Swift.