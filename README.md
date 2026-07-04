# ReviewGuard-Pro
An ultimate review &amp; testimonial plugin with smart negative feedback filtering, automatic page generation, dynamic social proof pop-ups, and Google SEO Rich Snippets (Schema) integration.


# ReviewGuard Pro - Smart Testimonials & Feedback System

[English](#english) | [Türkçe](#türkçe)

---

## English

**ReviewGuard Pro** is an ultimate, all-in-one premium WordPress testimonial and review plugin designed to boost your conversion rates while protecting your business reputation.

### 🌟 Key Features
*   **Smart Negative Filter:** Reviews with 4 or 5 stars are saved for admin approval. Reviews with 3 stars or lower are **never published**; instead, they are sent directly to the admin's email as a private support ticket.
*   **Automated Setup:** Automatically creates a sleek "Service Evaluation Form" page (`/hizmet-degerlendirme`) upon activation.
*   **AJAX Powered:** Form submissions are processed instantly without reloading the page, featuring a modern loading spinner.
*   **Social Proof Live Pop-ups:** Displays beautiful, non-intrusive live notifications (toast pop-ups) at the bottom-left corner showing recent 5-star reviews.
*   **Google SEO Rich Snippets (Schema JSON-LD):** Automatically injects schema codes into pages so your website displays with golden stars (★★★★★) in Google search results.
*   **Photo & Avatar Support:** Users can optionally upload a profile picture or service proof. If empty, the plugin generates a modern letter-avatar from their name.
*   **Admin Reply System:** Reply to customer reviews from the WP Admin panel, displaying a structured "Official Response" below the review.

### 🚀 Shortcode Usage

#### 1. Submission Form
Used to display the review submission form. (Automatically added to the created page, but can be used anywhere).

[memnuniyet_formu]


Displaying Reviews & Stats

Displays the overall customer satisfaction dashboard along with approved reviews. It supports flexible attributes for customization.

    Display all approved reviews:
    Plaintext

    [yorumları_listele]


    Limit the number of reviews displayed (e.g., latest 3 reviews):
Plaintext

[yorumları_listele limit="3"]

Show only top-rated reviews on your homepage (e.g., only 5-star reviews):
Plaintext

[yorumları_listele min_puan="5"]

Combine both parameters (e.g., latest 6 reviews with at least 4 stars):
Plaintext

[yorumları_listele limit="6" min_puan="4"]


Türkçe

ReviewGuard Pro, işletmenizin itibarını korurken dönüşüm oranlarınızı artırmak için tasarlanmış, hepsi bir arada premium bir WordPress müşteri görüşleri ve inceleme eklentisidir.
🌟 Öne Çıkan Özellikler

    Akıllı Negatif Filtresi: 4 veya 5 yıldızlı yorumlar admin onayına gönderilir. 3 yıldız veya daha düşük puanlı yorumlar asla sitede yayınlanmaz; bunun yerine doğrudan kriz yönetimi için admin e-postasına gizli bir destek talebi olarak gönderilir.

    Otomatik Kurulum: Eklenti aktif edildiğinde sitenizde otomatik olarak şık bir "Hizmet Değerlendirme Formu" sayfası (/hizmet-degerlendirme) oluşturur.

    AJAX Desteği: Form gönderimleri sayfa yenilenmeden, modern bir yükleniyor animasyonu eşliğinde anında gerçekleşir.

    Canlı Sosyal Kanıt Bildirimleri: Sitenizin sol alt köşesinde, en son yapılan 5 yıldızlı yorumları gösteren şık pop-up bildirimler görüntüler.

    Google SEO Zengin Sonuçlar (Schema JSON-LD): Web sitenizin Google arama sonuçlarında altın yıldızlarla (★★★★★) görünmesini sağlamak için gerekli schema kodlarını arka plana otomatik ekler.

    Fotoğraf ve Avatar Desteği: Kullanıcılar isteğe bağlı olarak profil resmi veya hizmet kanıtı yükleyebilir. Fotoğraf yoksa, isimlerin baş harfinden modern bir avatar oluşturulur.

    Yönetici Yanıt Sistemi: Müşteri yorumlarına WP Admin panelinden yanıt verebilir, yorumun altında kurumsal bir "Yetkili Yanıtı" bloğu gösterebilirsiniz.

🚀 Kısa Kod (Shortcode) Kullanımı
1. Yorum Gönderme Formu

Yorum toplama formunu göstermek için kullanılır. (Eklenti aktif olduğunda otomatik sayfaya eklenir ancak istenen her yerde kullanılabilir).
Plaintext

[memnuniyet_formu]

2. Yorumları Listeleme ve İstatistik Paneli

Genel müşteri memnuniyeti tablosunu ve onaylanmış yorumları listeler. Özelleştirme için esnek parametreleri destekler.

    Tüm onaylı yorumları listelemek için:
    Plaintext

    [yorumları_listele]

    Listelenecek yorum sayısını sınırlandırmak için (Örn: En son 3 yorum):
    Plaintext

    [yorumları_listele limit="3"]

    Ana sayfanızda sadece yüksek puanlı yorumları göstermek için (Örn: Sadece 5 yıldızlar):
    Plaintext

    [yorumları_listele min_puan="5"]

    İki parametreyi birlikte kullanmak için (Örn: En az 4 yıldızlı en son 6 yorum):
    Plaintext

    [yorumları_listele limit="6" min_puan="4"]
