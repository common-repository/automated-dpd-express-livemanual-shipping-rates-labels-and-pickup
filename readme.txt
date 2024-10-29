=== Automated DPD Shipping – HPOS supported ===
Contributors: aarsiv
Tags: DPD, DPD shipping, automated, shipping rates, shipping label
Requires at least: 4.0.1
Tested up to: 6.7
Requires PHP: 5.6
Stable tag: 2.0.1
License: GPLv3 or later License
URI: http://www.gnu.org/licenses/gpl-3.0.html

(Fully automated) shipping label, pickup, invoice, multi vendor,etc. supports all countries. 

== Description ==

[DPD shipping](https://wordpress.org/plugins/automated-dpd-express-livemanual-shipping-rates-labels-and-pickup/) plugin, integrate the [DPD Shipping](https://dpd.ie/) for delivery in Domestic and Internationally. According to the destination, We are providing all kind of DPD Services. It supports all Countries.

Annoyed of clicking button to create shipping label and generating it here is a hassle free solution, [Shipi](https://myshipi.com) is the tool with fully automated will reduce your cost and will save your time. 

Further, We will track your shipments and will update the order status automatically.


We are providing domestic & international shipping of DPD


= BACK OFFICE (SHIPPING ): =

[DPD shipping](https://wordpress.org/plugins/automated-dpd-express-livemanual-shipping-rates-labels-and-pickup/) plugin is deeply integrated with [Shipi](https://myshipi.com). So the shipping labels will be generated automatically. You can get the shipping label through email or from the order page.

 This plugin also supported the manual shipments option. By using this you can create the shipments directly from the order page. [Shipi](https://myshipi.com) will keep track of the orders and update the order state to complete.

= Useful filters =

1) Filter to show rates

> add_filter('hitstacks_dpdshipping_rate_cost', 'dpd_ship_cost', 10, 2);
> function dpd_ship_cost($rate_data = [], $order_sub_total = 0){
> 	return ["name" => "DPD Shiping", "cost" => 0]; // return rate and name for DPD courier
> }

= Your customer will appreciate : =

* The Product is delivered very quickly. The reason is, there this no delay between the order and shipping label action.
* Access to the many services of DPD for domestic & international shipping.
* Good impression of the shop.


= Shipi Action Sample =
[youtube https://www.youtube.com/watch?v=TZei_H5NkyU]


= Informations for Configure plugin =

> If you have already a DPD Account, please contact DPD to get your credentials.
> If you are not registered yet, please contact our customer service.
> Functions of the module are available only after receiving your API’s credentials.
> Please note also that phone number registration for the customer on the address webform should be mandatory.
> Create account in Shipi.

Plugin Tags: <blockquote>DPD, DPD SHIPPING, dpdshipping, dpdgroup ,DPD Express shipping, DPD Woocommerce, dpd for woocommerce, official dpd express, dpd plugin, dpd shipping plugin, create shipment, shipping plugin, dpd shipping rates</blockquote>


= About DPD =

DPDgroup is an international parcel delivery service for sorter compatible parcels weighing under 30 kg that delivers 7.5 million parcels worldwide every day. Its brands are DPD, Chronopost, Seur and BRT. The company is based in France and operates mainly in the express road-based market

= About HITStacks =

We are Web Development Company. We are planning for make everything automated. 

= What HITStacks Tell to Customers? =

> "Configure & take rest"

== Screenshots ==
1. DPD Account integration settings.
2. Shipper address configuration.
3. Packing algorithm configurations.
4. Shipping rates configuration & shipping services list.
5. Shipping label, tracking, pickup configuration.
6. Order page where you can easily get labels.
7. Checkout - Order placed with the DPD carrier.
8. Create shiping label screen in edit order page. This is for manual usage.
9. Shipping label management section - Shipi.
10. Check tracking informations - Shipi.
11. Check tracking informations - In the site my account section.
12. Shipping label & Management information.


== Changelog ==

= 2.0.1 =
	> Wordpress version tested

= 2.0.0 =
	> Added HPOS support
