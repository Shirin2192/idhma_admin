<?php
ob_start();
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *"); // or use a specific domain instead of '*'
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('form_validation');
    }
	public function dashboard()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['total_users'] = $this->model->selectWhereData('tbl_users', array('is_delete' => '1'), 'count(id) as total_users', true);
			$response['total_enquiries'] = $this->model->selectWhereData('tbl_enquiries', array('is_delete' => '1'), 'count(id) as total_enquiries', true);
			$response['total_team_members'] = $this->model->selectWhereData('tbl_team_members', array('is_delete' => '1'), 'count(id) as total_team_members', true);
			$this->load->view('admin/dashboard',$response);
		}
	}
	public function blogs()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/blogs');
		}
	}
	public function save_blogs() {
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		$this->form_validation->set_rules('slug', 'Slug', 'required|trim|is_unique[tbl_blogs.slug]');
		$this->form_validation->set_rules('content', 'Content', 'required');	
		$this->form_validation->set_rules('featured_image', 'Featured Image', 'callback_file_check');
		$this->form_validation->set_message('file_check', 'Please select a valid image file.');
		
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
		// Handle File Upload
		$featured_image = null;
		if (!empty($_FILES['featured_image']['name'])) {
			$config['upload_path'] = './uploads/blogs/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = time() . '_' . $_FILES['featured_image']['name'];
			$config['overwrite'] = false;
	
			$this->load->library('upload', $config);
	
			if (!$this->upload->do_upload('featured_image')) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Image upload failed.',
					'upload_error' => strip_tags($this->upload->display_errors())
				]);
				return;
			} else {
				$featured_image = $this->upload->data('file_name');
			}
		}
	
		$data = [
			'title' => $this->input->post('title'),
			'slug' => $this->input->post('slug'),
			'content' => $this->input->post('content'),
			'featured_image' => $featured_image,
		];
	
		$insert = $this->model->insertData('tbl_blogs', $data);
	
		if ($insert) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Blog saved successfully.'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save blog. Please try again.'
			]);
		}
	}
	public function file_check($str) {
		if (empty($_FILES['featured_image']['name'])) {
			$this->form_validation->set_message('file_check', 'Please select a file to upload.');
			return FALSE;
		} else {
			$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
			$file_type = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
	
			if (!in_array($file_type, $allowed_types)) {
				$this->form_validation->set_message('file_check', 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');
				return FALSE;
			}
		}
		return TRUE;
	}
	public function blogs_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_blogs', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	} 
	public function blogs_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_blogs', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_blog() {
		$response = ['status' => 'error'];
	
		$this->load->library('form_validation');
	
		// Set validation rules
		$this->form_validation->set_rules('edit_title', 'Title', 'required');
		$this->form_validation->set_rules('edit_slug', 'Slug', 'required');
		$this->form_validation->set_rules('edit_content', 'Content', 'required');
		$this->form_validation->set_rules('edit_status', 'Status', 'required');
	
		if ($this->form_validation->run() == FALSE) {
			$response['errors'] = [
				'title' => form_error('edit_title'),
				'slug' => form_error('edit_slug'),
				'content' => form_error('edit_content'),
				'status' => form_error('edit_status'),
			];
			echo json_encode($response);
			return;
		}
	
		$id = $this->input->post('edit_blog_id');
		$title = $this->input->post('edit_title');
		$slug = $this->input->post('edit_slug');
		$content = $this->input->post('edit_content');
		$status = $this->input->post('edit_status');
	
		// Default to no image change
		$featured_image = '';
	
		// Handle image upload
		if (!empty($_FILES['edit_featured_image']['name'])) {
			$config['upload_path'] = './uploads/blogs/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = time() . '_' . $_FILES['edit_featured_image']['name'];
			$config['overwrite'] = FALSE;
	
			$this->load->library('upload', $config);
	
			if ($this->upload->do_upload('edit_featured_image')) {
				$upload_data = $this->upload->data();
				$featured_image = $upload_data['file_name'];
			} else {
				$response['errors'] = ['featured_image' => $this->upload->display_errors()];
				echo json_encode($response);
				return;
			}
		}
	// Prepare data for update
		$data = [
			'title' => $title,
			'slug' => $slug,
			'content' => $content,
			'status' => $status,
		];
	
		if (!empty($featured_image)) {
			$data['featured_image'] = $featured_image;
		}
	
		// Update the blog post
		$updated = $this->model->updateData('tbl_blogs', $data, ['id' => $id]);
	
		if ($updated) {
			$response['status'] = 'success';
			$response['message'] = 'Blog updated successfully.';
		} else {
			$response['message'] = 'No changes were made or update failed.';
		}
	
		echo json_encode($response);
	}
	public function delete_blog()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_blogs', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Blog soft deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to soft delete blog.']);
		}
	}	
	public function export_member_data()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/export_member_data');
		}
	}
	public function export_member_data_on_datatable(){
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['data'] = $this->model->selectWhereData('tbl_users', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
			$response['status'] = 'success';
			echo json_encode($response);
		}
	}
	public function export_member_data_on_id(){
		$id = $this->input->post('id');
		$this->load->model("Admin_model");
		$response['data'] = $this->Admin_model->export_member_data_on_id($id);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function membership_category()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/membership_category');
		}
	}
	public function save_category() {
		$this->load->library('form_validation');
	
		// Updated validation rules
		$this->form_validation->set_rules('category_name', 'Category Name', 'required|trim|max_length[100]');
		$this->form_validation->set_rules('description', 'Description', 'required|trim|max_length[1000]');
	
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
	
		// Sanitize input values
		$category_name = trim($this->input->post('category_name'));
		$description = trim($this->input->post('description'));
	
		// Check for existing category name (case-insensitive)
		$existing_category = $this->model->selectWhereData(
			'tbl_membership_categories',
			['LOWER(category_name)' => strtolower($category_name)],
			'*',
			true
		);	
		if ($existing_category) {
			echo json_encode([
				'status' => 'error',
				'errors' => [
					'category_name' => 'Category name already exists.'
				]
			]);
			return;
		}
		// Prepare data to insert
		$data = [
			'category_name' => $category_name,
			'description' => $description
		];
	
		// Insert data
		$insert = $this->model->insertData('tbl_membership_categories', $data);
	
		if ($insert) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Category saved successfully.'
			]);
		} else {
			log_message('error', 'Category insert failed: ' . json_encode($data));
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save category. Please try again.'
			]);
		}
	}
	
	public function category_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_membership_categories', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	} 
	public function category_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_membership_categories', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_category() {
		$response = ['status' => 'error'];
		$this->load->library('form_validation');
	
		// Validation rules
		$this->form_validation->set_rules('edit_category_name', 'Category Name', 'required|trim');
		$this->form_validation->set_rules('edit_description', 'Description', 'required|trim');
	
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
	
	
		$id = $this->input->post('edit_category_id');
		$category_name = trim($this->input->post('edit_category_name'));
		$description = trim($this->input->post('edit_description'));
	
		// Check for duplicate name (excluding the current ID)
		$existing_category = $this->model->selectWhereData(
			'tbl_membership_categories',
			['category_name' => $category_name, 'id !=' => $id],
			'*',
			true
		);
	
		if ($existing_category) {
			$response['errors'] = [
				'edit_category_name' => 'Category name already exists.'
			];
			echo json_encode($response);
			return;
		}
	
		// Prepare update
		$data = [
			'category_name' => $category_name,
			'description' => $description
		];
	
		$updated = $this->model->updateData('tbl_membership_categories', $data, ['id' => $id]);
	
		if ($updated) {
			$response['status'] = 'success';
			$response['message'] = 'Category updated successfully.';
		} else {
			$response['message'] = 'No changes were made or update failed.';
		}
	
		echo json_encode($response);
	}
	
	public function delete_category()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_membership_categories', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete category.']);
		}
	}
	public function membership_types()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['categories'] = $this->model->selectWhereData('tbl_membership_categories', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
			$response['currencies'] = $this->model->selectWhereData('tbl_currency', array(), '*', false,array('id'=>'desc'));
			$this->load->view('admin/membership_types',$response);
		}
	}
	public function save_membership_type() {
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('category_name', 'Category', 'required');
		$this->form_validation->set_rules('type_name', 'Type Name', 'required|trim|max_length[100]');
		$this->form_validation->set_rules('currency', 'Currency', 'required|trim|max_length[10]');
		$this->form_validation->set_rules('price', 'Price', 'required|numeric');
		$this->form_validation->set_rules('short_description', 'Short Description', 'required|trim');
		$this->form_validation->set_rules('full_description', 'Full Description', 'required|trim');
	
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
		$category_id = $this->input->post('category_name');
		$type_name = trim($this->input->post('type_name'));
	
		// Check for existing type under same category
		$exists = $this->model->selectWhereData('tbl_membership_types',
			[ 'LOWER(type_name)' => strtolower($type_name)],
			'*',
			true
		);
	
		if ($exists) {
			echo json_encode([
				'status' => 'error',
				'errors' => ['type_name' => 'This membership type already exists under the selected category.']
			]);
			return;
		}
	
		$data = [
			'fk_category_id' => $category_id,
			'type_name' => $type_name,
			'fk_currency_id' => $this->input->post('currency'),
			'price' => $this->input->post('price'),
			'short_description' => $this->input->post('short_description'),
			'full_description' => $this->input->post('full_description'),
		];
		$insert = $this->model->insertData('tbl_membership_types', $data);
	
		if ($insert) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Membership type saved successfully.'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save membership type. Please try again.'
			]);
		}
	}
	public function membership_type_data_on_datatable(){
		$this->load->model('Admin_model');
		$response['data'] = $this->Admin_model->membership_type_data_on_datatable();
		$response['status'] = 'success';
		echo json_encode($response);
	} 
	public function membership_type_data_on_id(){
		$this->load->model('Admin_model');
		$id = $this->input->post('id');
		$response['data'] = $this->Admin_model->membership_type_data_on_id($id);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_membership_type()
	{
		$this->load->library('form_validation');
		$response = ['status' => false, 'errors' => [], 'message' => ''];

		// Gather the posted data
		$id = $this->input->post('edit_membership_type_id');
		$category_id = $this->input->post('edit_category_name');
		$type_name = trim($this->input->post('edit_type_name'));
		$currency_id = $this->input->post('edit_currency');
		$price = trim($this->input->post('edit_price'));
		$short_desc = trim($this->input->post('edit_short_description'));
		$full_desc = trim($this->input->post('edit_full_description'));

		// === Form Validation ===
		$this->form_validation->set_rules('edit_category_name', 'Category', 'required');
		$this->form_validation->set_rules('edit_type_name', 'Type Name', 'required');
		$this->form_validation->set_rules('edit_currency', 'Currency', 'required');
		$this->form_validation->set_rules('edit_price', 'Price', 'required|numeric');
		$this->form_validation->set_rules('edit_short_description', 'Short Description', 'required');
		$this->form_validation->set_rules('edit_full_description', 'Full Description', 'required');

		// Run the form validation
		if ($this->form_validation->run() == FALSE) {
			// Return validation errors if validation fails
			$response['status'] = 'error'; // Explicitly setting this here
			$response['errors'] = [
				'edit_category_name' => form_error('edit_category_name'),
				'edit_type_name' => form_error('edit_type_name'),
				'edit_currency' => form_error('edit_currency'),
				'edit_price' => form_error('edit_price'),
				'edit_short_description' => form_error('edit_short_description'),
				'edit_full_description' => form_error('edit_full_description'),
			];
		} else {
			// === Duplicate Check ===
			$duplicate = $this->model->selectWhereData(
				'tbl_membership_types',
				['fk_category_id' => $category_id, 'LOWER(type_name)' => strtolower($type_name), 'id !=' => $id],
				'*',
				true
			);

			// Handle duplicate case
			if ($duplicate) {
				$response['status'] = 'error';
				$response['errors'] = ['edit_type_name' => 'This type name already exists for the selected category.'];
			} else {
				// === Perform Update ===
				$data = [
					'fk_category_id' => $category_id,
					'type_name' => $type_name,
					'fk_currency_id' => $currency_id,
					'price' => $price,
					'short_description' => $short_desc,
					'full_description' => $full_desc,
				];

				// Update record in the database
				$updateSuccess = $this->model->updateData('tbl_membership_types', $data, ['id' => $id]);

				if ($updateSuccess) {
					$response['status'] = 'success';
					$response['message'] = 'Membership Type updated successfully.';
				} else {
					$response['status'] = 'error';
					$response['message'] = 'Failed to update the Membership Type.';
				}
			}
		}

		// Return the response as JSON
		echo json_encode($response);
	}
	public function delete_membership_type()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_membership_types', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Membership Type deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete category.']);
		}
	}
	public function team_member()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/team_member');
		}
	}
	public function add_team_member()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('designation', 'Designation', 'required|trim');
		$this->form_validation->set_rules('facebook_link', 'Facebook Link', 'required|trim');
		$this->form_validation->set_rules('linkedin_link', 'Linkedin Link', 'required|trim');
		$this->form_validation->set_rules('youtube_link', 'Youtube Link', 'required|trim');
		$this->form_validation->set_rules('twitter_link', 'Twitter Link', 'required|trim');
		$this->form_validation->set_rules('description', 'Description', 'required|trim');

		if (empty($_FILES['photo']['name'])) {
			$this->form_validation->set_rules('photo', 'Photo', 'required');
		}

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		} else {
			// Handle file upload
			$config['upload_path']   = './uploads/team/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['max_size']      = 2048;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('photo')) {
				echo json_encode([
					'status' => 'error',
					'errors' => ['photo' => $this->upload->display_errors()]
				]);
			} else {
				$uploadData = $this->upload->data();
				$exist = $this->model->selectWhereData(
					'tbl_team_members',
					['name' => $this->input->post('name'),'is_delete' => '1'],
					'*',
					true
				);
				if ($exist) {
					echo json_encode([
						'status' => 'error',
						'errors' => ['name' => 'This team member already exists.']
					]);
				} else {
					$data = [
						'name'          => $this->input->post('name'),
						'designation'         => $this->input->post('designation'),
						'photo'         => 'uploads/team/' . $uploadData['file_name'],
						'facebook_link' => $this->input->post('facebook_link'),
						'linkedin_link' => $this->input->post('linkedin_link'),
						'youtube_link'  => $this->input->post('youtube_link'),
						'twitter_link'  => $this->input->post('twitter_link'),
						'description'  => $this->input->post('description'),
					];
					$this->model->insertData('tbl_team_members', $data);
					echo json_encode(['status' => true, 'message' => 'Team member added successfully!']);
				}	
			}
		}
	}
	public function team_member_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_team_members', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function team_member_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_team_members', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_team_member()
{
    $this->load->library('form_validation');

    // Validate standard form fields (excluding file upload)
    $this->form_validation->set_rules('edit_name', 'Name', 'required|trim');
    $this->form_validation->set_rules('edit_designation', 'Designation', 'required|trim');
    $this->form_validation->set_rules('edit_facebook_link', 'Facebook Link', 'required|trim');
    $this->form_validation->set_rules('edit_linkedin_link', 'Linkedin Link', 'required|trim');
    $this->form_validation->set_rules('edit_youtube_link', 'Youtube Link', 'required|trim');
    $this->form_validation->set_rules('edit_twitter_link', 'Twitter Link', 'required|trim');
    $this->form_validation->set_rules('edit_description', 'Description', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode([
            'status' => 'error',
            'errors' => $this->form_validation->error_array()
        ]);
        return;
    }

    $photoPath = $this->input->post('current_photo'); // fallback to current photo

    // Check if a new photo was uploaded
    if (!empty($_FILES['edit_photo']['name'])) {
        $config['upload_path']   = './uploads/team/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;
        $config['detect_mime']   = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('edit_photo')) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['edit_photo' => strip_tags($this->upload->display_errors())]
            ]);
            return;
        } else {
            $uploadData = $this->upload->data();
            $photoPath = 'uploads/team/' . $uploadData['file_name'];

            // Fetch and delete old photo if exists
            $oldData = $this->model->selectWhereData('tbl_team_members', ['id' => $this->input->post('edit_member_id')]);
            $oldPhoto = $oldData['photo'] ?? '';

            if (!empty($oldPhoto) && file_exists(FCPATH . $oldPhoto)) {
                unlink(FCPATH . $oldPhoto);
            }
        }
    }

    // Case-insensitive name duplicate check
    $this->db->where('name', strtolower($this->input->post('edit_name')));
    $this->db->where('id !=', $this->input->post('edit_member_id'));
    $exist = $this->db->get('tbl_team_members')->row_array();

    if ($exist) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['edit_name' => 'This team member already exists.']
        ]);
        return;
    }

    // Prepare updated data
    $data = [
        'name'           => $this->input->post('edit_name'),
        'designation'    => $this->input->post('edit_designation'),
        'photo'          => $photoPath,
        'facebook_link'  => $this->input->post('edit_facebook_link'),
        'linkedin_link'  => $this->input->post('edit_linkedin_link'),
        'youtube_link'   => $this->input->post('edit_youtube_link'),
        'twitter_link'   => $this->input->post('edit_twitter_link'),
        'description'    => $this->input->post('edit_description'),
    ];

    // Perform the update
    $this->model->updateData('tbl_team_members', $data, ['id' => $this->input->post('edit_member_id')]);

    // Send success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Team member updated successfully!',
        'edit_photo' => base_url($photoPath)
    ]);
}

	public function delete_team_member()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_team_members', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Team Member deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete Team Member.']);
		}
	}
	public function export_enquires_data()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/export_enquires_data');
		}
	}
	public function export_enquires_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_enquiries', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function export_enquires_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_enquiries', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}	

	public function announcement()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/announcement');
		}
	}
	public function banners()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/banners');
		}
	}
	public function save_banners() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
		// Handle File Upload
		$banner = null;
		if (!empty($_FILES['banner']['name'])) {
			$config['upload_path'] = './uploads/banners/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = $_FILES['banner']['name'];
			$config['overwrite'] = false;
		
			$this->load->library('upload', $config);
		
			if (!$this->upload->do_upload('banner')) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Image upload failed.',
					'upload_error' => strip_tags($this->upload->display_errors())
				]);
				return;
			} else {
				$uploadData = $this->upload->data();
				$banner = $uploadData['file_name'];
			}
		}
		$data = [
			'title' => $this->input->post('title'),
			'banners' => $banner,
		];
		$insert = $this->model->insertData('tbl_banners', $data);
		if ($insert) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Banner saved successfully.'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save banner. Please try again.'
			]);
		}
	}
	public function banners_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_banners', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function banners_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_banners', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_banners() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('edit_title', 'Title', 'required|trim');
	
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
	
		$id = $this->input->post('edit_banner_id');
		$currentBanner = $this->input->post('current_banner'); // e.g. old_image.jpg
		$newBanner = $currentBanner; // fallback to current image
		$newFileUploaded = false;
		
	
		// Handle File Upload
		if (!empty($_FILES['edit_banner']['name'])) {
			$config['upload_path'] = './uploads/banners/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = $_FILES['edit_banner']['name'];
			$config['overwrite'] = false;
	
			$this->load->library('upload', $config);
	
			if (!$this->upload->do_upload('edit_banner')) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Image upload failed.',
					'upload_error' => strip_tags($this->upload->display_errors())
				]);
				return;
			} else {
				$uploadData = $this->upload->data();
				$newBanner = $uploadData['file_name'];
				$newFileUploaded = true;
			}
		}
	
		$data = [
			'title'   => $this->input->post('edit_title'),
			'banners' => $newBanner,
		];
		$updated = $this->model->updateData('tbl_banners', $data, ['id' => $id]);
	
		if ($updated) {
			// Delete only if a new image was uploaded
			if ($newFileUploaded && $newBanner !== $currentBanner && file_exists('./uploads/banners/' . $currentBanner)) {
				unlink('./uploads/banners/' . $currentBanner);
			}
	
			echo json_encode([
				'status' => 'success',
				'message' => 'Banner updated successfully.',
				'banner_path' => base_url('uploads/banners/' . $newBanner)
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to update banner.'
			]);
		}
	}
	
	public function delete_banners()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_banners', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Banner deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete Banner.']);
		}	
	}

	public function journal_pdfs()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/journal_pdfs');
		}
	}	
	public function save_journal_pdf() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
		// Handle File Upload
		$journal_pdf = null;
		if (!empty($_FILES['journal_pdf']['name'])) {
			$config['upload_path'] = './uploads/journal_pdfs/';
			$config['allowed_types'] = 'pdf';
			$config['file_name'] = $_FILES['journal_pdf']['name'];
			$config['overwrite'] = false;
		
			$this->load->library('upload', $config);
		
			if (!$this->upload->do_upload('journal_pdf')) {
				echo json_encode([
					'status' => 'error',
					'message' => 'PDF upload failed.',
					'upload_error' => strip_tags($this->upload->display_errors())
				]);
				return;
			} else {
				$uploadData = $this->upload->data();
				$journal_pdf = $uploadData['file_name'];
			}
		}
	
		if (empty($journal_pdf)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'No PDF file uploaded.'
			]);
			return;
		}
	
		if (empty($this->input->post('title'))) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Title is required.'
			]);
			return;
		}
		$data = [
			'title' => $this->input->post('title'),
			'file_path' => $journal_pdf,
		];
		$insert = $this->model->insertData('tbl_journals', $data);
		if ($insert) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Journal PDF saved successfully.'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save Journal PDF. Please try again.'
			]);
		}
	}
	public function journal_pdf_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_journals', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function journal_pdf_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_journals', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_journal_pdf()
	{
		$this->load->library('form_validation');

		// Validate input fields
		$this->form_validation->set_rules('edit_title', 'Title', 'required|trim');
		$this->form_validation->set_rules('edit_pdf_id', 'PDF ID', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors'  => $this->form_validation->error_array()
			]);
			return;
		}

		// Gather form data
		$id = $this->input->post('edit_pdf_id');
		$title = $this->input->post('edit_title');
		$currentFile = $this->input->post('edit_current_pdf'); // existing file name
		$newFile = $currentFile; // fallback
		$newFileUploaded = false;

		// Handle file upload if a new file is selected
		if (!empty($_FILES['edit_pdf']['name'])) {
			$config['upload_path'] = './uploads/journal_pdfs/';
			$config['allowed_types'] = 'pdf';
			$config['file_name'] = time() . '_' . $_FILES['edit_pdf']['name'];
			$config['overwrite'] = false;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('edit_pdf')) {
				echo json_encode([
					'status' => 'error',
					'message' => 'PDF upload failed.',
					'upload_error' => strip_tags($this->upload->display_errors())
				]);
				return;
			} else {
				$uploadData = $this->upload->data();
				$newFile = $uploadData['file_name'];
				$newFileUploaded = true;
			}
		}

		// Prepare data for update
		$data = [
			'title'     => $title,
			'file_path' => $newFile
		];
		
		// Update the record
		$updated = $this->model->updateData('tbl_journals', $data, ['id' => $id]);

		if ($updated) {
			// If new file was uploaded, delete the old one
			if ($newFileUploaded && $newFile !== $currentFile && file_exists('./uploads/journal_pdfs/' . $currentFile)) {
				unlink('./uploads/journal_pdfs/' . $currentFile);
			}

			echo json_encode([
				'status' => 'success',
				'message' => 'Journal PDF updated successfully.',
				'file_path' => base_url('uploads/journal_pdfs/' . $newFile)
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to update journal PDF.'
			]);
		}
	}
	public function delete_journal_pdf()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid Blog ID.']);
			return;
		}
		// Update blog status to "deleted" or "inactive"
		$data = [
			'is_delete' => '0', // Assuming 0 means deleted
		];
		$updated = $this->model->updateData('tbl_journals', $data, ['id' => $id]);

		if ($updated) {
			echo json_encode(['status' => 'success', 'message' => 'Journal PDF deleted successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to delete Journal PDF.']);
		}
	}
	public function about_us()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['about_us'] = $this->model->selectWhereData('tbl_about_us', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
			$response['about_us'] = $response['about_us'][0] ?? null; // Get the first record or null if not found	

			$this->load->view('admin/about_us', $response);
		}
	}
	public function update_about_us()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('about_us', 'About Us', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response['status'] = 'error';
			$response['message'] = 'Validation failed.';
			$response['errors'] = $this->form_validation->error_array();
			echo json_encode($response);
			// Handle error or redirect
		} else {
			$about_us_id = $this->input->post('about_us_id');
			$content = trim($this->input->post('about_us'));
			$exists = $this->model->CountWhereRecord(
				'tbl_about_us',
				['about_us' => strtolower($content), 'is_delete' => '1']
			);
			if ($exists >0) {
				$this->model->updateData('tbl_about_us', ['about_us' => $content], ['id' => $about_us_id]);
				$response['status'] = 'success';
				$response['message'] = 'About Us updated successfully.';
				echo json_encode($response);
			}else{
				$this->model->insertData('tbl_about_us', ['about_us' => $content]);
				$response['status'] = 'success';
				$response['message'] = 'About Us added successfully.';
				echo json_encode($response);
			}			
		}
	}
	public function objectives(){
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['objectives'] = $this->model->selectWhereData('tbl_objectives', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
			$response['objectives'] = $response['objectives'][0] ?? null; // Get the first record or null if not found	
			$this->load->view('admin/objectives', $response);
		}
	}
	public function update_objective()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('objectives', 'Objectives', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response['status'] = 'error';
			$response['message'] = 'Validation failed.';
			$response['errors'] = $this->form_validation->error_array();
			echo json_encode($response);
			// Handle error or redirect
		} else {
			$objective_id = $this->input->post('objective_id');
			$objectives = trim($this->input->post('objectives'));
			$exists = $this->model->CountWhereRecord(
				'tbl_objectives',
				['objectives' => strtolower($objectives), 'is_delete' => '1']
			);
			if ($exists >0) {
				$this->model->updateData('tbl_objectives', ['objectives' => $objectives], ['id' => $objectives_id]);
				$response['status'] = 'success';
				$response['message'] = 'Objectives updated successfully.';
				echo json_encode($response);
			}else{
				$this->model->insertData('tbl_objectives', ['objectives' => $objectives]);
				$response['status'] = 'success';
				$response['message'] = 'Objectives added successfully.';
				echo json_encode($response);
			}			
		}
	}
	public function membership_benefits(){
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$response['membership_benefits'] = $this->model->selectWhereData('tbl_member_benefits', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
			$response['membership_benefits'] = $response['membership_benefits'][0] ?? null; // Get the first record or null if not found	
			$this->load->view('admin/member_benefits', $response);
		}
	}
	public function save_update_member_benefits() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title_benefits', 'Title', 'required|trim');
		$this->form_validation->set_rules('member_benefits', 'Member Benefits', 'required|trim');
		$this->form_validation->set_rules('title_activities', 'Title', 'required|trim');
		$this->form_validation->set_rules('activities_of_ihdma', 'Activities of IHDMA', 'required|trim');
	
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
	
		$title_benefits = trim($this->input->post('title_benefits'));
		$member_benefits = trim($this->input->post('member_benefits'));
		$title_activities = trim($this->input->post('title_activities'));
		$activities_of_ihdma = trim($this->input->post('activities_of_ihdma'));
	
		$data = [
			'title_benefits' => $title_benefits,
			'benefits' => $member_benefits,
			'title_activities' => $title_activities,
			'activities' => $activities_of_ihdma,
		];
	
		// Check if a record already exists with the same benefits & activities (ignoring deleted ones)
		// $where = [
		// 	'benefits' => $member_benefits,
		// 	'activities' => $activities_of_ihdma,
		// 	'is_delete' => '1' // Assuming 1 means active
		// ];
	
		$existing = $this->model->selectWhereData('tbl_member_benefits',array('is_delete'=>'1') , '*', true);
		if ($existing) {
			// Update existing record
			$update = $this->model->updateData('tbl_member_benefits', $data, ['id' => $existing['id']]);
			if ($update) {
				echo json_encode([
					'status' => 'success',
					'message' => 'Membership benefits updated successfully.'
				]);
			} else {
				echo json_encode([
					'status' => 'error',
					'message' => 'Failed to update membership benefits. Please try again.'
				]);
			}
		} else {
			// Insert new record
			$insert = $this->model->insertData('tbl_member_benefits', $data);
			if ($insert) {
				echo json_encode([
					'status' => 'success',
					'message' => 'Membership benefits saved successfully.'
				]);
			} else {
				echo json_encode([
					'status' => 'error',
					'message' => 'Failed to save membership benefits. Please try again.'
				]);
			}
		}
	}
	 public function save_announcement() {
        $title = $this->input->post('title', TRUE);
        $message = $this->input->post('message', TRUE);
        $send_email = $this->input->post('send_email') ? 1 : 0;
        $send_whatsapp = $this->input->post('send_whatsapp') ? 1 : 0;
		
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
    	$this->form_validation->set_rules('message', 'Message', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
        $data = [
            'title' => $title,
            'message' => $message,
            'send_email' => $send_email,
            'send_whatsapp' => $send_whatsapp
        ];
        $insert_id = $this->model->insertData('tbl_announcements',$data);

        if ($insert_id) {
			$this->load->library('email');
			$this->load->model('Member_model');
            // 🔹 Send Bulk Email
            // if ($send_email) {
            //     $recipients = $this->Member_model->get_email_recipients();
            //     foreach ($recipients as $recipient) {
            //         $this->send_email($recipient->email, $title, $message);
            //         sleep(1); // Optional: avoid spam detection
            //     }
            // }

            // // 🔹 Send Bulk WhatsApp
            // if ($send_whatsapp) {
            //     $recipients = $this->Member_model->get_whatsapp_recipients();
            //     foreach ($recipients as $recipient) {
            //         $this->send_whatsapp_notification($recipient['phone'], $title, $message);
            //         sleep(1); // Optional: to avoid API rate limits
            //     }
            // }

            echo json_encode(['status' => 'success', 'message' => 'Announcement sent successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send announcement.']);
        }
    }

    private function send_whatsapp_notification($to_number, $title, $message) {
        $sid = 'YOUR_TWILIO_SID';
        $token = 'YOUR_TWILIO_AUTH_TOKEN';
        $twilio_number = 'whatsapp:+14155238886';

        $client = new Client($sid, $token);

        try {
            $client->messages->create(
                "whatsapp:$to_number",
                [
                    'from' => $twilio_number,
                    'body' => "📢 *$title*\n\n$message"
                ]
            );
        } catch (Exception $e) {
            log_message('error', "WhatsApp error for $to_number: " . $e->getMessage());
        }
    }
	public function announcement_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_announcements', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function announcement_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_announcements', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_announcement() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('edit_title', 'Title', 'required|trim');
		$this->form_validation->set_rules('edit_message', 'Message', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Validation failed.',
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}
		$id = $this->input->post('edit_announcement_id');
		$title = $this->input->post('edit_title', TRUE);
		$message = $this->input->post('edit_message', TRUE);
		$send_email = $this->input->post('edit_send_email') ? 1 : 0;
		$send_whatsapp = $this->input->post('edit_send_whatsapp') ? 1 : 0;

		$data = [
			'title' => $title,
			'message' => $message,
			'send_email' => $send_email,
			'send_whatsapp' => $send_whatsapp
		];
		$updated = $this->model->updateData('tbl_announcements',$data, ['id' => $id]);

		if ($updated) {
			$this->load->library('email');
			$this->load->model('Member_model');
			// 🔹 Send Bulk Email
			// if ($send_email) {
			//     $recipients = $this->Member_model->get_email_recipients();
			//     foreach ($recipients as $recipient) {
			//         $this->send_email($recipient['email'], $title, $message);
			//         sleep(1); // Optional: avoid spam detection
			//     }
			// }

			// 🔹 Send Bulk WhatsApp
			if ($send_whatsapp) {
			    $recipients = $this->Member_model->get_whatsapp_recipients();
			    foreach ($recipients as $recipient) {
			        $this->send_whatsapp_notification($recipient['phone'], $title, $message);
			        sleep(1); // Optional: to avoid API rate limits
			    }
			}

			echo json_encode(['status' => 'success', 'message' => 'Announcement updated successfully.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to update announcement.']);
		}
	}
	public function renderHBOTNotices()
	{
		$admin_session = $this->session->userdata('admin_session');
		if (empty($admin_session)) {
			redirect('common');
		}else{
			$this->load->view('admin/hbot_notices');
		}
	}
	public function save_HBOT_Notices()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Title', 'required|is_unique[tbl_hbot_notifications.title]');
		$this->form_validation->set_rules('button_name', 'Button Label', 'required');
		// $this->form_validation->set_rules('link', 'Video Link', 'required');

		if ($this->form_validation->run() == FALSE) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Validation failed.',
					'errors' => $this->form_validation->error_array()
				]);
				return;
			}

		// Handle file upload
		$file_path = '';
		if (!empty($_FILES['files']['name'])) {
			$upload_dir = './uploads/hbot/';
			
			// Create directory if not exists
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true); // recursive = true
			}

			$config['upload_path'] = $upload_dir;
			$config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|mp4';
			$config['file_name'] = time() . '_' . $_FILES['files']['name'];

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('files')) {
				$file_path = $upload_dir . $this->upload->data('file_name');
			} else {
				echo json_encode([
					'status' => 'error',
					'errors' => ['files' => $this->upload->display_errors()]
				]);
				return;
			}
		} else {
			echo json_encode(['status' => 'error', 'errors' => ['files' => 'File is required.']]);
			return;
		}
		// Insert into DB
		$data = [
			'title' => $this->input->post('title'),
			'description' => $this->input->post('description'),
			'video_link' => $this->input->post('link'),
			'file_path' => $file_path,
			'button' => $this->input->post('button_name'),
		];
		$this->model->insertData('tbl_hbot_notifications', $data);

		echo json_encode(['status' => 'success']);
	}
	public function HBOT_Notices_data_on_datatable(){
		$response['data'] = $this->model->selectWhereData('tbl_hbot_notifications', array('is_delete' => '1'), '*', false,array('id'=>'desc'));
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function HBOT_Notices_data_on_id(){
		$id = $this->input->post('id');
		$response['data'] = $this->model->selectWhereData('tbl_hbot_notifications', array('id' => $id), '*', true);
		$response['status'] = 'success';
		echo json_encode($response);
	}
	public function update_HBOT_Notices_data(){
		$this->load->library('form_validation');

		$this->form_validation->set_rules('edit_title', 'Title', 'required');
        $this->form_validation->set_rules('edit_button_name', 'Button Label', 'required');

		// $this->form_validation->set_rules('link', 'Video Link', 'required');

		if ($this->form_validation->run() == FALSE) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Validation failed.',
					'errors' => $this->form_validation->error_array()
				]);
				return;
		}
			$id = $this->input->post('edit_hbot_id');
            $title = $this->input->post('edit_title');
            $description = $this->input->post('edit_description');
            $video_link = $this->input->post('edit_link');
            $button_name = $this->input->post('edit_button_name');
            $current_file = $this->input->post('edit_current_file'); // current file path in DB

			$new_file = null;
            if ($_FILES['edit_file']['name']) {
                // File upload configuration
                $config['upload_path'] = './uploads/hbot/';
                $config['allowed_types'] = 'pdf|doc|docx|jpg|png'; // Modify as per allowed file types
                $config['max_size'] = 5000; // 5MB limit

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('edit_file')) {
                    // Get new file path
                    $upload_data = $this->upload->data();
                    $new_file = './uploads/hbot/' . $upload_data['file_name'];
                } else {
                    // Handle file upload error
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            } else {
                // No new file, keep the existing one
                $new_file = $current_file;
            }
		// Insert into DB
			  $update_data = [
                'title' => $title,
                'description' => $description,
                'video_link' => $video_link,
                'button' => $button_name,
                'file_path' => $new_file // Update file path
            ];

			$update_result = $this->model->updateData('tbl_hbot_notifications', $update_data,array('id'=>$id));
			if ($update_result) {
                echo json_encode(['status' => 'success', 'message' => 'HBOT Notification updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update HBOT Notification.']);
            }
	}
		




}
	