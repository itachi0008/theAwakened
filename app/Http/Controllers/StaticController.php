<?php

namespace App\Http\Controllers;

class StaticController extends QuintypeController
{
    public function aboutUs()
    {
        return view('static/about_us', $this->toView([]));
    }

    public function ContactUs()
    {
        return view('static/contact_us', $this->toView([]));
    }

    public function privacyPolicy()
    {
        return view('static/privacy_policy', $this->toView([]));
    }

    public function termsOfUse()
    {
        return view('static/terms_of_use', $this->toView([]));
    }
}
