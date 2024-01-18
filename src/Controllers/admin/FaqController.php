<?php

namespace App\Controllers\admin;

use App\Models\Faq;

class FaqController
{
    const URL_FAQ_INDEX = '/admin/faq.php';
    const URL_FAQ_CREATE = '../../../view/admin/faq/faq_create.php';


    /**
     * Display FAQ in support.php file
     */
    public static function home()
    {
        return FAQ::getAllFaqList();
    }

    /**
     * Display FAQ in Dashboard
     */
    public function index()
    {
        $faqs = FAQ::getAllFaqList();
        $faqs = $this->hydrate($faqs);

        require_once base_path('view/admin/faq/index.php');
    }

    public function displayCreateForm()
    {

        redirectAndExit(self::URL_FAQ_CREATE);

        //require_once base_path('view/admin/faq/faq_create.php');
    }


    /**
     * create FAQ
     */
    public function storeFaq(): void
    {
        // Prepare POST
        $question =$_POST['question'] ?? '';
        $answer = $_POST['answer'] ?? '';

        $faq = new FAQ();


        if(!$question and !$answer){
            $this->hydrate($_POST);
            $faq->save();
        } else {
            $_SESSION['message'] = "Erreur lors de l'enregistrement";
            $_SESSION['type'] = 'danger';
        }

        redirectAndExit(self::URL_FAQ_INDEX);
    }


    /**
     * Update FAQ
     */
    public function updateFaq(): void
    {
        // Prepare POST
        $id =$_GET['idFaq'] ?? '';
        $question = $_POST['question'] ?? '';
        $answer = $_POST['answer'] ?? '';

        $faq = new FAQ();

        if(!$id and !$question and !$answer){
            $faq->hydrate($_POST);
            $faq->update();
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour";
            $_SESSION['type'] = 'danger';
        }


        redirectAndExit(self::URL_FAQ_INDEX);
    }


    /**
     * Delete FAQ
     */
    public function deleteFaq(): void
    {
        // Prepare GET
        $id = $_GET['idFaq'] ?? '';

        $faq = new FAQ();

        if($id){
            $faq->setId($_GET['idFaq']);
            $faq->delete();
        } else {
            $_SESSION['message'] = "Erreur de traitement";
            $_SESSION['type'] = 'danger';
        }

        redirectAndExit(self::URL_FAQ_INDEX);
    }


    /**
     * Hydrate FAQ
     */
    private function hydrate(array $faqs) : array
    {
        foreach ($faqs as $key => $faq) {
            $faqs[$key] = Faq::hydrate($faq);
        }
        return $faqs;
    }
}