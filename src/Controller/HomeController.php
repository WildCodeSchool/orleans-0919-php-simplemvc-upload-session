<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use \DateInterval;
use \DatePeriod;
use \DateTime;

class HomeController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(int $month = 12)
    {
        $startMonth = new DateTime('01-' . $month . '-2019');
        $endMonth = new DateTime('24-' . $month . '-2019 23:59:59');

        $days = new DatePeriod($startMonth, new DateInterval('P1D'), $endMonth);

        $shuffleDays = [];
        foreach ($days as $day) {
            $shuffleDays[] = $day;
        }
        if (empty($_SESSION['openDays'])) {
            shuffle($shuffleDays);
            foreach ($shuffleDays as $shuffleDay) {
                $_SESSION['openDays'][] = ['day' => $shuffleDay, 'status' => false];
            }
        }

        return $this->twig->render('Home/index.html.twig', [
            'days' => $_SESSION['openDays'],
        ]);
    }

    public function open(string $pos)
    {
        $_SESSION['openDays'][$pos]['status'] = true;
        header('Location: /home/index');
    }

    public function reset()
    {
        session_destroy();
        header('Location: /home/index');
    }
}
