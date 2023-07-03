<?php
/**
 * Plugin Name:       PF Modal Edit
 * Description:       【PF純正プラグイン】モーダル画面を追加します。
 * Version:           0.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pfmodal
 *
 * @package           create-block
 */

/*__________ブロック登録__________*/

function create_block_pfmodal_block_init() {
	register_block_type( __DIR__ . '/build', array(
		'attributes'      => array(
			'selectedSlug' => array(
				'type'     => 'string',
				'default'  => '',
			),
		),
	) );
}
add_action( 'init', 'create_block_pfmodal_block_init' );



/*__________管理画面(カスタム投稿タプ)追加__________*/

function create_post_type_modal() {
    register_post_type( 'modal',
        array(
            'labels' => array(
                'name' => __( 'モーダル画面' ),
                'singular_name' => __( 'モーダル画面' ),
                'add_new' => __( '新規追加' ),
                'add_new_item' => __( '新規モーダル画面を追加' ),
                'edit_item' => __( 'モーダル画面を編集' ),
                'new_item' => __( '新規モーダル画面' ),
                'view_item' => __( 'モーダル画面を見る' ),
                'search_items' => __( 'モーダル画面を探す' ),
                'not_found' => __( 'モーダル画面はありません' ),
                'not_found_in_trash' => __( 'ゴミ箱にモーダル画面はありません' ),
            ),
            'public' => false,  // 外部からは見えないようにする
            'show_ui' => true,  // 管理画面のメニューには表示する
            'has_archive' => false,  // アーカイブページは持たない
            'show_in_rest' => true,  // REST APIには公開する
            'supports' => array( 'title', 'editor' ),  // タイトルとエディタをサポートする
            'menu_position' => 6,  // メニューの位置を指定する
        )
    );
}
add_action( 'init', 'create_post_type_modal' );



/*__________メタボックスの追加__________*/

function modal_custom_metabox() {
    add_meta_box(
        'modal_metabox', // メタボックスのID
        'モーダルボタンのテキスト：', // メタボックスのタイトル
        'modal_metabox_callback', // コールバック関数
        'modal' // このメタボックスを表示する投稿タイプ
    );
}
add_action('add_meta_boxes', 'modal_custom_metabox');

function modal_metabox_callback($post) {
    $value = get_post_meta($post->ID, 'modal_meta_key', true);
    echo '<input type="text" id="modal_metabox_input" name="modal_metabox_input" value="' . esc_attr($value) . '" />';
}



/*__________メタボックスの保存__________*/

function save_modal_metabox($post_id) {
    if (array_key_exists('modal_metabox_input', $_POST)) {
        update_post_meta(
            $post_id,
            'modal_meta_key',
            $_POST['modal_metabox_input']
        );
    }
}
add_action('save_post', 'save_modal_metabox');



/*__________ショートコード__________*/

function open_modal_shortcode($atts) {
	ob_start(); // Start output buffering to capture all subsequent output
	$modal_id = $atts[0]; // Get the slug from the shortcode attributes

	$args = array(
        'post_type' => 'modal',
        'name' => $atts[0],
        'post_status' => 'publish',
        'posts_per_page' => 1
    );

    // 新しい WP_Query オブジェクトを作成します
    $modal_query = new WP_Query($args);
    $custom_text = "";

	// ループを開始します
    if ($modal_query->have_posts()) {
        while($modal_query->have_posts()) {
            $modal_query->the_post();
            $custom_text = get_post_meta(get_the_ID(), 'modal_meta_key', true); // メタデータを取得します
        }
    }
    wp_reset_postdata();
	?>

	<div class="open-modal">
		<input id="modalCheck-<?php echo $modal_id; ?>" type="checkbox">
		<label for="modalCheck-<?php echo $modal_id; ?>" class="open">
			<?php
			if($custom_text){
                echo $custom_text; 
            }else{
                echo '開く';
            }
			?>
		</label>
		<div class="open-modal-content">
			<div class="open-modal-content-inner">
				<label for="modalCheck-<?php echo $modal_id; ?>" class="close">&times;</label>
				<?php
				$args = array(
					'post_type' => 'modal',
					'name' => $atts[0],
					'post_status' => 'publish',
					'posts_per_page' => 1
				);

				// 新しい WP_Query オブジェクトを作成します
				$modal_query = new WP_Query($args);

				// ループを開始します
				if ($modal_query->have_posts()) {
					while($modal_query->have_posts()) {
						$modal_query->the_post();
						the_content(); // 投稿のコンテンツを表示します
					}
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>

	<?php
	return ob_get_clean(); // Return the buffered output
}
add_shortcode('modal', 'open_modal_shortcode');


/*__________追加JSの読み込み__________*/
function my_enqueue_scripts() {
    // 正しいパスとスクリプトの依存関係（この場合はjQuery）を指定します
    wp_enqueue_script('add-script', plugins_url('add-script.js', __FILE__), array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');


/*__________ブロックパターンを追加：モーダル画面用__________*/
register_block_pattern_category('modal', ['label' => 'modal']);
register_block_pattern(
	'modal',
	array(
		'title'       => __( 'modal1' ),
		'categories'  => ["modal"],
		'content'     => '
		aaa'
	)
);