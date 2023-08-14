<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }

        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }

        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }

        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
        }

        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }

        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
        }

        p {
            margin-top: 1.5rem;
        }

        .footer {
            margin-top: 2rem;
            border-top: 1px solid #efefef;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }

        a:active,
        a:link,
        a:visited {
            color: #dd4814;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .error-text h1 {
            font-size: 5rem;
            margin: 0;
            color: #e74c3c;
        }

        .error-text p {
            margin: 0;
            font-size: 1.5rem;
            color: #555;
        }

        .astronaut img {
            max-width: 100%;
            height: auto;
            margin-top: 2rem;
            animation: float 5s infinite ease-in-out alternate;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body>
    <!-- <div class="wrap">
        <h1>404</h1>
        <p>
        </p>
    </div> -->
    <div class="error-container">
        <div class="error-text">
            <h1>404</h1>
            <p>Something wrong!</p>
            <p>
                <?php if (ENVIRONMENT !== 'production') : ?>
                    <?= nl2br(esc($message)) ?>
                <?php else : ?>
                    <?= lang('Errors.sorryCannotFind') ?>
                <?php endif ?>
            </p>
        </div>
        <div class="astronaut">
            <img src="<?= base_url('icon/android-chrome-192x192.png') ?>" alt="KCS">
        </div>
        <a href="<?= site_url('/'); ?>">Back to Home</a>
    </div>
</body>

</html>