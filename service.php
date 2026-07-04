<?php
/**
 * Plugin Name: ReviewGuard Pro - Smart Testimonials & Feedback System
 * Description: An ultimate review plugin with smart negative feedback filtering, automatic page generation, dynamic social proof pop-ups, and Google SEO Rich Snippets (Schema) integration.
 * Version: 4.0.0
 * Author: Görkem Efe Dersin
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. ADIM: Aktivasyonda Sayfa Oluşturma
register_activation_hook( __FILE__, 'ump_create_page' );
function ump_create_page() {
    if ( ! get_page_by_path('hizmet-degerlendirme') ) {
        wp_insert_post(array(
            'post_title'    => 'Hizmet Değerlendirme Formu',
            'post_content'  => '[memnuniyet_formu]',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'hizmet-degerlendirme',
            'comment_status'=> 'closed'
        ));
    }
}

// 2. ADIM: Custom Post Type Tanımı
add_action( 'init', function() {
    register_post_type( 'hizmet_yorum', array(
        'labels' => array('name' => 'Hizmet Yorumları', 'singular_name' => 'Yorum', 'all_items' => 'Tüm Yorumlar', 'add_new' => 'Yeni Ekle'),
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-testimonial', 'supports' => array( 'title', 'editor' )
    ));
});

// 3. ADIM: Admin Meta Kutuları (Geliştirilmiş: Admin Yanıtı ve Fotoğraf Dahil)
add_action( 'add_meta_boxes', function() { add_meta_box('ump_detay', 'Yorum & Yönetici Detayları', 'ump_meta_cb', 'hizmet_yorum', 'normal', 'high'); });
function ump_meta_cb( $post ) {
    $email = get_post_meta( $post->ID, '_ump_email', true );
    $rating = get_post_meta( $post->ID, '_ump_rating', true );
    $photo = get_post_meta( $post->ID, '_ump_photo', true );
    $reply = get_post_meta( $post->ID, '_ump_admin_reply', true );
    ?>
    <p><label><strong>E-posta:</strong></label><br><input type="email" name="ump_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></p>
    <p><label><strong>Puan:</strong></label><br>
        <select name="ump_rating">
            <?php for($i=1; $i<=5; $i++) { echo '<option value="'.$i.'" '.selected($rating, $i, false).'>'.$i.' Yıldız</option>'; } ?>
        </select>
    </p>
    <?php if($photo): ?>
        <p><label><strong>Müşteri Fotoğrafı:</strong></label><br><img src="<?php echo esc_url($photo); ?>" style="max-width:100px; border-radius:8px; margin-top:5px;"></p>
    <?php endif; ?>
    <p><label><strong>Yönetici (Admin) Yanıtı:</strong></label><br>
        <textarea name="ump_admin_reply" rows="4" style="width:100%; max-width:500px;" placeholder="Müşteriye teşekkür edin veya yanıt verin..."><?php echo esc_textarea($reply); ?></textarea>
    </p>
    <?php
}
add_action( 'save_post', function( $post_id ) {
    if ( isset($_POST['ump_rating']) ) update_post_meta( $post_id, '_ump_rating', sanitize_text_field($_POST['ump_rating']) );
    if ( isset($_POST['ump_email']) ) update_post_meta( $post_id, '_ump_email', sanitize_email($_POST['ump_email']) );
    if ( isset($_POST['ump_admin_reply']) ) update_post_meta( $post_id, '_ump_admin_reply', sanitize_textarea_field($_POST['ump_admin_reply']) );
});

// 4. ADIM: Form Kısa Kodu (Dosya Yükleme Desteği [enctype])
add_shortcode( 'memnuniyet_formu', function() {
    ob_start(); ?>
    <div class="hpme-form-card">
        <h3>Hizmetimizi Değerlendirin</h3>
        <p class="hpme-subtitle">Görüşleriniz hizmet kalitemizi artırmak için çok değerlidir.</p>
        <div id="hpme-form-result"></div>
        
        <form id="hpme-ajax-form" enctype="multipart/form-data">
            <?php wp_nonce_field( 'ump_secure_nonce', 'ump_nonce' ); ?>
            <div class="hpme-input-row">
                <div class="hpme-group"><label>Adınız Soyadınız *</label><input type="text" name="ad_soyad" required></div>
                <div class="hpme-group"><label>E-posta Adresiniz *</label><input type="email" name="email" required></div>
            </div>
            <div class="hpme-input-row">
                <div class="hpme-group">
                    <label>Memnuniyet Dereceniz *</label>
                    <div class="hpme-star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required/><label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4"/><label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3"/><label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2"/><label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1"/><label for="star1">★</label>
                    </div>
                </div>
                <div class="hpme-group">
                    <label>Profil Fotoğrafınız (Opsiyonel)</label>
                    <input type="file" name="avatar_file" accept="image/*" style="padding:7px 10px;">
                </div>
            </div>
            <div class="hpme-group"><label>Yorumunuz *</label><textarea name="yorum" rows="4" required></textarea></div>
            <button type="submit" class="hpme-btn">Değerlendirmeyi Gönder <span class="hpme-spinner"></span></button>
        </form>
    </div>
    <?php return ob_get_clean();
});

// 5. ADIM: AJAX İşleme (Güvenli Görsel Yükleme + Kötü Yorum Filtresi)
add_action( 'wp_ajax_ump_submit_form', 'ump_handle_ajax' );
add_action( 'wp_ajax_nopriv_ump_submit_form', 'ump_handle_ajax' );
function ump_handle_ajax() {
    check_ajax_referer( 'ump_secure_nonce', 'ump_nonce' );

    $ad_soyad = sanitize_text_field($_POST['ad_soyad']);
    $email    = sanitize_email($_POST['email']);
    $rating   = intval($_POST['rating']);
    $yorum    = sanitize_textarea_field($_POST['yorum']);

    if( empty($ad_soyad) || empty($email) || empty($rating) || empty($yorum) ) {
        wp_send_json_error('Lütfen tüm alanları doldurun.');
    }

    // Negatif Yorum Filtresi (3 ve Altı Puan)
    if ( $rating <= 3 ) {
        $to = get_option( 'admin_email' );
        $subject = '⚠️ Düşük Hizmet Memnuniyeti Bildirimi: ' . $ad_soyad;
        $body = "Müşteri: $ad_soyad\nE-posta: $email\nPuan: $rating / 5\nMesajı:\n$yorum";
        wp_mail( $to, $subject, $body );
        wp_send_json_success('Geri bildiriminiz doğrudan yönetim ekibimize iletilmiştir. Durumu düzeltmek için en kısa sürede iletişime geçeceğiz.');
    }

    // Pozitif Yorumları Kaydetme
    $post_id = wp_insert_post(array(
        'post_title' => $ad_soyad, 'post_content' => $yorum, 'post_status' => 'pending', 'post_type' => 'hizmet_yorum'
    ));

    if($post_id) {
        update_post_meta($post_id, '_ump_email', $email);
        update_post_meta($post_id, '_ump_rating', $rating);

        // Fotoğraf Yükleme Kontrolü
        if ( ! empty( $_FILES['avatar_file']['name'] ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            
            $attachment_id = media_handle_upload( 'avatar_file', $post_id );
            if ( ! is_wp_error( $attachment_id ) ) {
                $file_url = wp_get_attachment_url( $attachment_id );
                update_post_meta( $post_id, '_ump_photo', $file_url );
            }
        }
        wp_send_json_success('Harika! Değerlendirmeniz başarıyla alındı. Teşekkür ederiz.');
    }
    wp_send_json_error('Bir hata oluştu.');
}

// 6. ADIM: Gelişmiş Parametreli Listeleme ve Admin Yanıt Gösterimi [yorumları_listele]
add_shortcode( 'yorumları_listele', function($atts) {
    ob_start();
    
    // Kısa kod parametreleri (Örn: [yorumları_listele limit="6" min_puan="4"])
    $a = shortcode_atts( array(
        'limit' => -1,
        'min_puan' => 1,
    ), $atts );

    $all_posts = get_posts(array('post_type' => 'hizmet_yorum', 'post_status' => 'publish', 'numberposts' => -1));
    $total_reviews = count($all_posts);
    $avg_rating = 0;
    
    if($total_reviews > 0) {
        $sum = 0;
        foreach($all_posts as $p) { $sum += intval(get_post_meta($p->ID, '_ump_rating', true)); }
        $avg_rating = round($sum / $total_reviews, 1);
        ?>
        <script type="application/ld+json">
        {"@context": "https://schema.org","@type": "Product","name": "<?php echo esc_attr(get_bloginfo('name')); ?>","aggregateRating": {"@type": "AggregateRating","ratingValue": "<?php echo $avg_rating; ?>","reviewCount": "<?php echo $total_reviews; ?>","bestRating": "5","worstRating": "1"}}
        </script>
    <?php }

    if($total_reviews > 0): ?>
    <div class="hpme-stats-bar">
        <div class="hpme-stat-box"><span class="hpme-stat-num"><?php echo $avg_rating; ?></span><span class="hpme-stat-stars"><?php echo str_repeat('★', round($avg_rating)) . str_repeat('☆', 5 - round($avg_rating)); ?></span><span class="hpme-stat-lbl"><?php echo $total_reviews; ?> İnceleme</span></div>
        <div class="hpme-stat-text"><h4>Müşteri Deneyim Skorları</h4><p>Gerçek kullanıcılar tarafından yapılan doğrulanmış incelemeler.</p></div>
    </div>
    <?php endif;

    // Filtrelenmiş Yorumları Getir
    $filtered_posts = array();
    foreach($all_posts as $post) {
        $rating = intval(get_post_meta($post->ID, '_ump_rating', true));
        if($rating >= intval($a['min_puan'])) {
            $filtered_posts[] = $post;
        }
    }

    if($a['limit'] > 0) {
        $filtered_posts = array_slice($filtered_posts, 0, intval($a['limit']));
    }

    if ( !empty($filtered_posts) ) {
        echo '<div class="hpme-reviews-grid">';
        foreach($filtered_posts as $post) {
            $rating = get_post_meta($post->ID, '_ump_rating', true);
            $photo = get_post_meta($post->ID, '_ump_photo', true);
            $reply = get_post_meta($post->ID, '_ump_admin_reply', true);
            $harf = mb_substr($post->post_title, 0, 1, 'UTF-8');
            ?>
            <div class="hpme-review-wrapper">
                <div class="hpme-review-card">
                    <div class="hpme-card-header">
                        <?php if($photo): ?>
                            <img src="<?php echo esc_url($photo); ?>" class="hpme-avatar" style="object-fit:cover;">
                        <?php else: ?>
                            <div class="hpme-avatar"><?php echo strtoupper($harf); ?></div>
                        <?php endif; ?>
                        <div class="hpme-meta-info">
                            <span class="hpme-author"><?php echo esc_html($post->post_title); ?></span>
                            <span class="hpme-stars"><?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?></span>
                        </div>
                    </div>
                    <div class="hpme-body"><?php echo wpautop(esc_html($post->post_content)); ?></div>
                    <div class="hpme-footer"><?php echo get_the_date('', $post->ID); ?></div>
                </div>
                
                <!-- Yönetici Yanıt Bloğu -->
                <?php if(!empty($reply)): ?>
                <div class="hpme-admin-reply-box">
                    <div class="hpme-reply-title">📢 Yetkili Yanıtı:</div>
                    <div class="hpme-reply-content"><?php echo esc_html($reply); ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php
        }
        echo '</div>';
    } else { echo '<p class="hpme-empty">Görüntülenecek yorum bulunamadı.</p>'; }
    return ob_get_clean();
});

// 7. ADIM: Canlı Bildirim Pop-up Script ve AJAX Tetikleyici (FormData Güncellemesi ile)
add_action( 'wp_footer', function() { 
    $recent_reviews = get_posts(array('post_type' => 'hizmet_yorum', 'post_status' => 'publish', 'numberposts' => 5));
    if (empty($recent_reviews)) return;
    
    $js_reviews = array();
    foreach($recent_reviews as $r) {
        $js_reviews[] = array(
            'title' => esc_html($r->post_title),
            'rating'=> str_repeat('★', intval(get_post_meta($r->ID, '_ump_rating', true))),
            'time'  => human_time_diff(get_the_time('U', $r->ID), current_time('timestamp')) . ' önce'
        );
    }
    ?>
    <div id="uhpme-toast-notification" class="uhpme-toast">
        <div class="uhpme-toast-avatar">✓</div>
        <div class="uhpme-toast-content">
            <span class="uhpme-toast-name" id="uhp-toast-name">Ahmet Y.</span>
            <span class="uhpme-toast-stars" id="uhp-toast-stars">★★★★★</span>
            <span class="uhpme-toast-desc">Hizmetimizi puanladı. (<span id="uhp-toast-time">Az önce</span>)</span>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('hpme-ajax-form');
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(form); // Fotoğrafları gönderebilmek için FormData
                formData.append('action', 'ump_submit_form');
                var btn = form.querySelector('.hpme-btn');
                btn.disabled = true;
                btn.querySelector('.hpme-spinner').style.display = 'inline-block';
                
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    var resDiv = document.getElementById('hpme-form-result');
                    if(data.success) {
                        resDiv.innerHTML = '<div class="hpme-msg success">'+data.data+'</div>';
                        form.reset();
                    } else { resDiv.innerHTML = '<div class="hpme-msg error">'+data.data+'</div>'; }
                    btn.disabled = false;
                    btn.querySelector('.hpme-spinner').style.display = 'none';
                });
            });
        }

        var reviews = <?php echo json_encode($js_reviews); ?>;
        if(reviews.length > 0) {
            var toast = document.getElementById('uhpme-toast-notification');
            var index = 0;
            function showToast() {
                var current = reviews[index];
                document.getElementById('uhp-toast-name').innerText = current.title;
                document.getElementById('uhp-toast-stars').innerText = current.rating;
                document.getElementById('uhp-toast-time').innerText = current.time;
                toast.classList.add('show');
                setTimeout(function() { toast.classList.remove('show'); index = (index + 1) % reviews.length; }, 4000);
            }
            setTimeout(showToast, 3000);
            setInterval(showToast, 12000);
        }
    });
    </script>
<?php });

// 8. ADIM: CSS Tasarımları
add_action( 'wp_head', function() { ?>
    <style>
        .hpme-form-card { max-width: 600px; margin: 40px auto; padding: 35px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; font-family: sans-serif; }
        .hpme-form-card h3 { margin: 0 0 5px 0; font-size: 24px; color: #1e293b; }
        .hpme-subtitle { color: #64748b; margin-bottom: 25px; font-size: 14px; }
        .hpme-input-row { display: flex; gap: 20px; }
        .hpme-group { flex: 1; margin-bottom: 20px; display: flex; flex-direction: column; }
        .hpme-group label { font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px; }
        .hpme-group input, .hpme-group textarea { padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; background: #f8fafc; }
        .hpme-group input:focus, .hpme-group textarea:focus { border-color: #4f46e5; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        .hpme-star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; font-size: 32px; gap: 4px; }
        .hpme-star-rating input { display: none; }
        .hpme-star-rating label { color: #cbd5e1; cursor: pointer; transition: 0.2s; }
        .hpme-star-rating input:checked ~ label, .hpme-star-rating label:hover, .hpme-star-rating label:hover ~ label { color: #fbbf24; }
        .hpme-btn { width: 100%; padding: 14px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .hpme-spinner { width: 16px; height: 16px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; display: none; animation: hpme-spin 0.8s linear infinite; }
        @keyframes hpme-spin { to { transform: rotate(360deg); } }
        .hpme-msg { padding: 14px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .hpme-msg.success { background: #dcfce7; color: #166534; }
        .hpme-msg.error { background: #fee2e2; color: #991b1b; }

        /* Pop-up */
        .uhpme-toast { position: fixed; bottom: -100px; left: 20px; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; padding: 15px 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px; z-index: 99999; transition: bottom 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); font-family: sans-serif; max-width: 300px; }
        .uhpme-toast.show { bottom: 20px; }
        .uhpme-toast-avatar { width: 35px; height: 35px; background: #10b981; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .uhpme-toast-content { display: flex; flex-direction: column; }
        .uhpme-toast-name { font-weight: 700; color: #1e293b; font-size: 14px; }
        .uhpme-toast-stars { color: #fbbf24; font-size: 12px; }
        .uhpme-toast-desc { font-size: 12px; color: #64748b; }

        /* Listeleme Grid & Yanıt Yapısı */
        .hpme-stats-bar { display: flex; align-items: center; background: #ffffff; border: 1px solid #e2e8f0; padding: 25px; border-radius: 16px; gap: 30px; margin-bottom: 30px; font-family: sans-serif; }
        .hpme-stat-box { display: flex; flex-direction: column; align-items: center; border-right: 2px solid #e2e8f0; padding-right: 30px; }
        .hpme-stat-num { font-size: 38px; font-weight: 800; color: #1e293b; }
        .hpme-stat-stars { color: #fbbf24; font-size: 18px; }
        .hpme-stat-lbl { font-size: 12px; color: #64748b; }
        .hpme-stat-text h4 { margin: 0 0 5px 0; color: #1e293b; }
        .hpme-stat-text p { margin: 0; color: #64748b; font-size: 14px; }
        
        .hpme-reviews-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; font-family: sans-serif; }
        .hpme-review-wrapper { display: flex; flex-direction: column; }
        .hpme-review-card { background: #ffffff; border: 1px solid #e2e8f0; padding: 25px; border-radius: 16px; display: flex; flex-direction: column; position: relative; z-index: 2; }
        .hpme-avatar { width: 45px; height: 45px; background: #6366f1; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; }
        .hpme-author { font-weight: 700; color: #1e293b; }
        .hpme-stars { color: #fbbf24; }
        .hpme-body { color: #475569; font-size: 14px; line-height: 1.6; }
        .hpme-footer { font-size: 11px; color: #94a3b8; text-align: right; margin-top: 15px; }
        
        /* Admin Yanıt Kutusu CSS */
        .hpme-admin-reply-box { background: #f8fafc; border: 1px solid #e2e8f0; border-top: none; padding: 15px 20px; border-radius: 0 0 16px 16px; margin-top: -10px; z-index: 1; padding-top: 20px; position: relative; border-left: 4px solid #6366f1; }
        .hpme-reply-title { font-size: 12px; font-weight: 700; color: #6366f1; margin-bottom: 4px; }
        .hpme-reply-content { font-size: 13px; color: #334155; line-height: 1.5; }
        
        @media(max-width: 600px) { .hpme-input-row { flex-direction: column; gap: 0; } .hpme-stats-bar { flex-direction: column; text-align: center; gap: 15px; } .hpme-stat-box { border: none; padding: 0; } }
    </style>
<?php });