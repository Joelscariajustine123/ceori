<?php
/**
 * Title: Contact – details, form and map
 * Slug: ceori/contact-main
 * Categories: ceori-pages
 * Description: Contact details and opening hours beside the appointment form, followed by an embedded map.
 * Viewport Width: 1440
 *
 * @package ceori
 */

?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"appointment","className":"ceori-section ceori-contact","layout":{"type":"constrained"}} -->
<section id="appointment" class="wp-block-group alignfull ceori-section ceori-contact"><!-- wp:columns {"align":"wide","className":"ceori-contact__cols"} -->
<div class="wp-block-columns alignwide ceori-contact__cols"><!-- wp:column {"width":"40%","className":"ceori-contact__info"} -->
<div class="wp-block-column ceori-contact__info" style="flex-basis:40%"><!-- wp:paragraph {"className":"ceori-eyebrow"} -->
<p class="ceori-eyebrow">Get in touch</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">We’re Here to <em>Help</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ceori-section__lead"} -->
<p class="ceori-section__lead">Reach out for appointments, questions about treatments or advice on where to start. Call, email or send the form and our team will guide you.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"ceori-info-card","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-info-card"><!-- wp:heading {"level":3,"className":"ceori-info-card__title"} -->
<h3 class="wp-block-heading ceori-info-card__title">CEORI Skin Clinic</h3>
<!-- /wp:heading -->

<!-- wp:ceori/clinic-info {"field":"contact"} /-->

<!-- wp:ceori/clinic-info {"field":"call"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ceori-info-card","layout":{"type":"default"}} -->
<div class="wp-block-group ceori-info-card"><!-- wp:heading {"level":3,"className":"ceori-info-card__title"} -->
<h3 class="wp-block-heading ceori-info-card__title">Opening hours</h3>
<!-- /wp:heading -->

<!-- wp:ceori/clinic-info {"field":"hours"} /-->

<!-- wp:heading {"level":3,"className":"ceori-info-card__title"} -->
<h3 class="wp-block-heading ceori-info-card__title">Follow us</h3>
<!-- /wp:heading -->

<!-- wp:ceori/clinic-info {"field":"social"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"ceori-contact__form"} -->
<div class="wp-block-column ceori-contact__form"><!-- wp:ceori/appointment-form /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:ceori/clinic-info {"field":"map","align":"wide"} /--></section>
<!-- /wp:group -->
