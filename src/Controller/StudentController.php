<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\StudentManager;

/**
 * Class StudentController
 *
 */
class StudentController extends AbstractController
{
    const MAX_FILE_SIZE = 100000;
    const ALLOWED_MIMES = ['image/jpeg', 'image/png'];


    /**
     * Display Student listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $studentManager = new StudentManager();
        $students = $studentManager->selectAll();

        return $this->twig->render('Student/index.html.twig', ['students' => $students]);
    }


    /**
     * Display Student informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        $studentManager = new StudentManager();
        $student = $studentManager->selectOneById($id);

        return $this->twig->render('Student/show.html.twig', ['student' => $student]);
    }


    /**
     * Display Student edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id): string
    {
        $studentManager = new StudentManager();
        $student = $studentManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student['firstname'] = $_POST['firstname'];
            $student['lastname'] = $_POST['lastname'];
            $student['path'] = $_POST['path'];
            $studentManager->update($student);
        }

        return $this->twig->render('Student/edit.html.twig', ['student' => $student]);
    }


    /**
     * Display Student creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            if (empty($data['firstname'])) {
                $errors[] = 'The firstname is empty';
            }
            // no error upload

            if (!empty($_FILES['path']['name'])) {
                $path = $_FILES['path'];

                if ($path['error'] !== 0) {
                    $errors[] = 'Upload error';
                }

                // size du fichier
                if ($path['size'] > self::MAX_FILE_SIZE) {
                    $errors[] = 'The file size should be < ' . (self::MAX_FILE_SIZE / 1000) . ' ko';
                }

                // type mime autorisés
                if (!in_array($path['type'], self::ALLOWED_MIMES)) {
                    $errors[] = 'Wrong type mime, the allowed mimes are ' . implode(', ', self::ALLOWED_MIMES);
                }
            }

            if (empty($errors)) {
                // finalisation de l'upload en déplacant le fichier dans le dossier upload
                if (!empty($path)) {
                    $fileName = uniqid() . '.' . pathinfo($path['name'], PATHINFO_EXTENSION);
                    move_uploaded_file($path['tmp_name'], UPLOAD_PATH . $fileName);
                }

                $studentManager = new StudentManager();
                $student = [
                    'firstname' => $_POST['firstname'],
                    'lastname'  => $_POST['lastname'],
                    'path'      => $fileName ?? '',
                ];
                $id = $studentManager->insert($student);
                header('Location:/Student/show/' . $id);
            }
        }

        return $this->twig->render('Student/add.html.twig', [
            'errors' => $errors ?? [],
        ]);
    }


    /**
     * Handle Student deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $studentManager = new StudentManager();
        $student = $studentManager->selectOneById($id);
        if ($student) {
            unlink(UPLOAD_PATH . $student['path']);
            $studentManager->delete($id);
        }

        header('Location:/Student/index');
    }
}
