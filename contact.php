<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/app.php';

if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    && is_array($_POST)
    && sizeof($_POST) > 0
) {
    if (
        !isset($_POST['iN'])
        || empty($_POST['iN'])
        || $_POST['iN'] === null
        || $_POST['iN'] === ''
    ) {
        $ms = "No name in post";
        goto end;
    }
    if (
        !isset($_POST['iT'])
        || empty($_POST['iT'])
        || $_POST['iT'] === null
        || $_POST['iT'] === ''
    ) {
        $ms = "No phone in post";
        goto end;
    }
    if (
        !isset($_POST['iE'])
        || empty($_POST['iE'])
        || $_POST['iE'] === null
        || $_POST['iE'] === ''
    ) {
        $ms = "No email in post";
        goto end;
    }
    if (
        !isset($_POST['tA'])
        || empty($_POST['tA'])
        || $_POST['tA'] === null
        || $_POST['tA'] === ''
    ) {
        $ms = "No message in post";
        goto end;
    }
    /*
    if (
        !isset($_POST['cc'])
        || empty($_POST['cc'])
        || $_POST['cc'] === null
        || $_POST['cc'] === ''
    ) {
        $ms = "No cc in post";
        goto end;
    }
    */

    /* ---------------------------------------------------------------------- */

    if (!cset('esd')) {
        //~ if sent email cookie, not set yet
        // add 1 at the end of nt before encrypt
        $esd = enc($nt . '1', 'e');
        // set cookie email sent
        setcookie('esd', $esd, dur(60 * 60));
    } else {
        //~ check if email per hour limit reached
        // decrypt email cookie
        $emc = enc($_COOKIE['esd'], 'd');
        // get last character
        $last = $emc[mb_strlen($emc) - 1];
        // if limit reached go to end
        if ($last == '2') {
            $jres = "try again in 1h";
            goto end;
        } else {
            // add 2 at the end of nt before encrypt
            $esd = enc($nt . '2', 'e');
            // set cookie email sent
            setcookie('esd', $esd, dur(60 * 60));
        }
    }

    /* ---------------------------------------------------------------------- */

    $name = clean($_POST['iN'], 'fl');
    // $ccd = spe($_POST['cc'], 'd'); // use when have cc, else this below:
    $ccd = "+1";
    $cco = clean($ccd, false);
    $phone = "+" . $cco . clean($_POST['iT'], false);
    $email = clean($_POST['iE'], 'lo');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $ms = 'Email ' . $email . ' valid error!';
        goto end;
    }
    $tA = clean($_POST['tA'], false);

    /* ---------------------------------------------------------------------- */

    //TODO: working only if sending smtp email, or to gmail addresses
    //~ to client
    /*
    mail(
        $email,
        "Contact",
        "\nHi " . $name . " You sent:\n\n" . $tA . "\n\nto,\n" . $dm . "\n\n",
        $from
    );
    sleep(2);
    */

    //~ to admin
    mail(
        $re,
        "Contact",
        "\nFrom: " . $name . "\n" . $email . "\n" . $phone . "\n" .
            $ip . "\n\nMessage:\n" . $tA . "\n\n",
        $from
    );

    //~ to logs
    // $ms = "Contact email from: " . $name . "\n" . $phone . "\n"
    //     . $ip . "\n" . $email . "\nMessage: " . $tA;

    $jres = "sent"; // sent
}

/* -------------------------------------------------------------------------- */

end:

if ($ms) {
    lg($lg, $p, $tm, $ms);
}

if ($jres === "e") {
    $jres = "error sending email";
}

echo $jres;
