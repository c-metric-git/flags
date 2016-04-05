<?php
/*
Plugin Name: mooontes Comments Media Upload
Plugin URI: http://mooontes.com
Description: Allows media upload within comments
Version: 0.1
Author: @mooontes
Author URI: http://mooontes.com
Author Email: javier@mooontes.com
License: GPL2
*/

class MooontesCommentsMediaUpload {

	const name = 'mooontes Comments Media Upload';
	const slug = 'mooontes-comments-media-upload';

	public function __construct() {

		$this->init_plugin_constants();

		add_action('wp_enqueue_scripts', 	array($this, 'enqueue_scripts'));
		add_filter('comment_form_defaults',	array($this, 'add_upload_input_to_comment_form'));
		add_action('comment_post', 			array($this, 'save_uploaded_picture'));
		add_filter('comment_text', 			array($this, 'set_img_tag'));
	}

	public function add_upload_input_to_comment_form($default)
	{
		$default['comment_field'] .= '
			<p>
				<label for="vote">' . __('Upload image or document') . '</label>
				<input type="file" id="mts_picture_upload" name="mts_picture_upload">
			</p>';

		return $default;
	}

	public function save_uploaded_picture($comment_id)
	{
		$comment 			= get_comment($comment_id);
		$comment_author_id 	= $comment->user_id;

		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}

		if (isset($_FILES['mts_picture_upload']['name']) &&
			trim($_FILES['mts_picture_upload']['name']) != '')

			foreach ($_FILES as $file => $array) {
				if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
					die("upload error : " . $_FILES[$file]['error']);
				}
				$attach_id = media_handle_upload($file, $post->ID);

				if (is_numeric($attach_id) && $attach_id > 0) {
					$attachment = array();
					$attachment['ID'] = $attach_id;
					if (is_numeric($_POST['user_id']) && $comment_author_id > 0) {
						$attachment['post_author'] = (int)$comment_author_id;
						wp_update_post($attachment);
					}

					$ext = pathinfo(wp_get_attachment_url($attach_id), PATHINFO_EXTENSION);

					if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'gif' || $ext === 'png') {
						$thumbnail_url 	= wp_get_attachment_image_src($attach_id, 'thumbnail');
						$picture_url	= wp_get_attachment_image_src($attach_id, 'large');
						wp_update_comment(array('comment_ID' => $comment_id, 'comment_content' => '[a href="' . $picture_url[0] . '"][img src="' . $thumbnail_url[0] . '"][/a] '. "\n" . $comment->comment_content));
					} else {
						wp_update_comment(array('comment_ID' => $comment_id, 'comment_content' => '[a href="' . wp_get_attachment_url($attach_id) . '"]' . basename(wp_get_attachment_url($attach_id)) . '[/a] '. "\n" . $comment->comment_content));
					}
				}
			}
	}

	public function set_img_tag($content)
	{
		$content = preg_replace('%\[a href="([^"]+)"\]\[img src="([^"]+)"\]\[/a\]%i', '<a href="$1"><img src="$2"></a>', $content);
		$content = preg_replace('%\[a href="([^"]+)"\]([^\[]+)\[/a\]%i', '<a href="$1">$2</a>', $content);

		return $content;
	}

	public function enqueue_scripts() {
		if (!is_admin()) {
			wp_enqueue_script(self::slug . '-script', WP_PLUGIN_URL . '/' . self::slug . '/js/display.js', array('jquery'));
		}
	}

	protected function init_plugin_constants() {
		if (!defined( 'PLUGIN_NAME')) {
		  define( 'PLUGIN_NAME', self::name );
		}

		if (!defined( 'PLUGIN_SLUG')) {
		  define( 'PLUGIN_SLUG', self::slug );
		}
	}
}

new MooontesCommentsMediaUpload();



