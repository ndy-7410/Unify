<!DOCTYPE html>
<html lang="fr">
<?php
session_start();
?>

<head>
    <meta charset="UTF-8" />
    <title>Unify | Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .contact-section {
        width: 100%;
        max-width: 40rem;
        margin-left: auto;
        margin-right: auto;
        padding: 3rem 1rem;
    }

    .contact-intro>*+* {
        margin-top: 1rem;
    }

    .contact-title {
        font-size: 1.875rem;
        line-height: 2.25rem;
        font-weight: 700;
    }

    .contact-description {
        color: rgb(107 114 128);
    }

    .form-group-container {
        display: grid;
        gap: 1rem;
        margin-top: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        margin-bottom: 0.5rem;
    }

    .form-input,
    .form-textarea {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        display: flex;
        height: 2.5rem;
        width: 100%;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    .form-input::placeholder,
    .form-textarea:focus-visible {
        color: #6b7280;
    }

    .form-input:focus-visible,
    .form-textarea:focus-visible {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
    }

    .form-textarea {
        min-height: 120px;
    }

    .form-submit {
        width: 100%;
        margin-top: 1.2rem;
        background-color: #3124ca;
        color: #fff;
        padding: 13px 5px;
        border-radius: 0.375rem;
    }
</style>

<body>
    <div>
        <?php
        include 'navbar.php';
        ?>
    </div>
    <div class="text-center mb-4">
        <h1>Nous contacter</h1>
        <p class="text-muted">Contacter Unify</p>
    </div>
    <!-- 
    This is a working contact form. To receive email, 
    Replace YOUR_ACCESS_KEY_HERE with your actual Access Key.

    Create Access Key here 👉 https://web3forms.com/
 -->

    <section class="contact-section">
        <div class="contact-intro">
            <!-- <h2 class="contact-title">Contacter Unify</h2> -->
            <!-- <p class="contact-description">
                Fill out the form below and we'll get back to you as soon as possible.
            </p> -->
        </div>

        <form class="contact-form" action="https://api.web3forms.com/submit" method="POST">

            <input type="hidden" name="access_key" value="e0e4b032-c218-4050-b47a-e04027259fc6" />
            <input type="hidden" name="subject" value="Nouveau message de Unify | Contacte" />
            <input type="hidden" name="from_name" value="Unify | Contacte" />
            <!-- More custom ization options available in the docs: https://docs.web3forms.com -->

            <div class="form-group-container">
                <div class="form-group">
                    <label for="name" class="form-label">Nom</label>
                    <input id="name" name="name" class="form-input" placeholder="" type="text" />
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Mail</label>
                    <input id="email" name="email" class="form-input" placeholder="" type="email" />
                </div>
                <!-- <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" name="phone" class="form-input" placeholder="+1 (234) 56789" type="text" />
                </div> -->
                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-textarea" id="message" name="message" placeholder=""></textarea>
                </div>
            </div>
            <button class="form-submit" type="submit">Envoyer le message</button>
        </form>
    </section>
    <div>
        <?php
        include 'footer.php';
        ?>
    </div>
</body>

</html>