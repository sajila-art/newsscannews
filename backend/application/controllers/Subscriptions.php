<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->ensure_table();
    }

    /**
     * List all newsletter_subscriptions records for admins.
     */
    public function index() {
        $this->require_roles(array('admin'));

        $status_filter = trim((string) $this->input->get('status', TRUE));
        $search = trim((string) $this->input->get('q', TRUE));

        if (!in_array($status_filter, array('subscribed', 'unsubscribed'), TRUE)) {
            $status_filter = '';
        }

        $total_count = (int) $this->db->count_all('newsletter_subscriptions');
        $subscribed_count = (int) $this->db
            ->where('status', 'subscribed')
            ->count_all_results('newsletter_subscriptions');
        $unsubscribed_count = (int) $this->db
            ->where('status', 'unsubscribed')
            ->count_all_results('newsletter_subscriptions');

        $this->db->from('newsletter_subscriptions');
        if ($status_filter !== '') {
            $this->db->where('status', $status_filter);
        }
        if ($search !== '') {
            $this->db->like('email', $search);
        }
        $rows = $this->db
            ->order_by('subscribed_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();

        $data = array(
            'title' => 'Newsletter Subscriptions',
            'rows' => $rows,
            'status_filter' => $status_filter,
            'search' => $search,
            'total_count' => $total_count,
            'subscribed_count' => $subscribed_count,
            'unsubscribed_count' => $unsubscribed_count,
        );

        $this->render('subscriptions/index', $data);
    }

    /**
     * Toggle a subscriber's status between subscribed and unsubscribed.
     */
    public function toggle($id = NULL) {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post' || !$id) {
            redirect('subscriptions');
            return;
        }

        $id = (int) $id;
        $row = $this->db
            ->where('id', $id)
            ->limit(1)
            ->get('newsletter_subscriptions')
            ->row_array();

        if (!$row) {
            $this->session->set_flashdata('error', 'Subscription record not found.');
            redirect('subscriptions');
            return;
        }

        if ($row['status'] === 'subscribed') {
            $this->db->where('id', $id)->update('newsletter_subscriptions', array(
                'status' => 'unsubscribed',
                'unsubscribed_at' => date('Y-m-d H:i:s'),
            ));
            $this->audit('unsubscribe', 'newsletter_subscriptions', $id, $row['email']);
            $this->session->set_flashdata('success', 'Marked ' . $row['email'] . ' as unsubscribed.');
        } else {
            $this->db->where('id', $id)->update('newsletter_subscriptions', array(
                'status' => 'subscribed',
                'subscribed_at' => date('Y-m-d H:i:s'),
                'unsubscribed_at' => NULL,
            ));
            $this->audit('resubscribe', 'newsletter_subscriptions', $id, $row['email']);
            $this->session->set_flashdata('success', 'Re-subscribed ' . $row['email'] . '.');
        }

        redirect('subscriptions');
    }

    /**
     * Delete a subscription record.
     */
    public function delete($id = NULL) {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post' || !$id) {
            redirect('subscriptions');
            return;
        }

        $id = (int) $id;
        $row = $this->db
            ->where('id', $id)
            ->limit(1)
            ->get('newsletter_subscriptions')
            ->row_array();

        if ($row) {
            $this->db->where('id', $id)->delete('newsletter_subscriptions');
            $this->audit('delete', 'newsletter_subscriptions', $id, $row['email']);
            $this->session->set_flashdata('success', 'Deleted subscription for ' . $row['email'] . '.');
        }

        redirect('subscriptions');
    }

    private function ensure_table() {
        $sql = "CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `status` ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed',
            `subscribed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `unsubscribed_at` DATETIME NULL,
            `ip_address` VARCHAR(45) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->db->query($sql);
    }
}
