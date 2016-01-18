<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories_model extends MY_Model{

	private $_categoriesTable 		= 'categories';
	private $_translationsType 		= 'category';
	private $_translationsTable 	= 'translations';


	/*	Poieranie drzewa kategorii	*/
	public function getCategoriesTree($parentID = 0, $level = 0, $lang = 'pl', $url = NULL){
		$whereArray = array(
			'translation_type' 		=> $this->_translationsType,
			'translation_lang' 		=> $lang,
			'category_parent_id' 	=> $parentID,
			'category_level' 		=> $level,
			);
		$query = $this->db->select('*')
			->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
			->where($whereArray)
			->order_by('category_sort', 'ASC')
			->get($this->_categoriesTable);

		if($query->num_rows() > 0){
			$content = '<ul class="list-inline">';
			foreach ($query->result_array() as $key) {

				$urlPath = $this->getCategoryPath($key['category_id'], $key['category_level']);
				$urlLink = $this->getCategoryLink($urlPath, $key['translation_lang']);

				$content .= '<li><a href="'.base_url().$urlLink.'" title="'.$key['translation_url_title'].'">'.$key['translation_value'].'</a>';
				$content .= $this->getCategoriesTree($key['category_id'], $level + 1, $lang, $url);
				$content .= '</li>';

			}
			$url = NULL;
			$content .= '</ul>';
			return $content;
		}
		else{
			return FALSE;
		}
	}

    /*	Poieranie drzewa kategorii	*/
    public function getCategoriesTreeAdmin($parentID = 0, $level = 0, $lang = 'pl', $url = NULL){
        $whereArray = array(
            'translation_type' 		=> $this->_translationsType,
            'translation_lang' 		=> $lang,
            'category_parent_id' 	=> $parentID,
            'category_level' 		=> $level,
        );
        $query = $this->db->select('*')
            ->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
            ->where($whereArray)
            ->order_by('category_sort', 'ASC')
            ->get($this->_categoriesTable);

        if($query->num_rows() > 0){
            $content = '<ul class="list-categories-admin">';
            foreach ($query->result_array() as $key) {
                $content .= '<li><a href="#" class="tool" title="
                    &lt;a href=&quot; '.base_url('categories/editCategoryByID/'.$key['category_id']).' &quot; &gt; &lt;span class=&quot; glyphicon glyphicon-edit &quot; &gt; &lt;/span&gt; Edytuj &lt;/a&gt;
                     |
                    &lt;a href=&quot; '.base_url('categories/addChildCategoryByID/'.$key['category_id']).' &quot; &gt; &lt;span class=&quot; glyphicon glyphicon-plus &quot; &gt; &lt;/span&gt; Dodaj podkategorię &lt;/a&gt;
                     |
                    &lt;a href=&quot; '.base_url('categories/deleteCategoryByID/'.$key['category_id']).' &quot; &gt; &lt;span class=&quot; glyphicon glyphicon-trash &quot; &gt; &lt;/span&gt; Usuń &lt;/a&gt;
                ">'.$key['translation_value'].'</a>';
                $content .= $this->getCategoriesTreeAdmin($key['category_id'], $level + 1, $lang, $url);
                $content .= '</li>';

            }
            $url = NULL;
            $content .= '</ul>';
            return $content;
        }
        else{
            return FALSE;
        }
    }

    public function getCategoriesTreeAdminAddNewCategory($parentID = 0, $level = 0, $lang = 'pl', $url = NULL){
        $whereArray = array(
            'translation_type' 		=> $this->_translationsType,
            'translation_lang' 		=> $lang,
            'category_parent_id' 	=> $parentID,
            'category_level' 		=> $level,
        );
        $query = $this->db->select('*')
            ->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
            ->where($whereArray)
            ->order_by('category_sort', 'ASC')
            ->get($this->_categoriesTable);

        if($query->num_rows() > 0){
            $content = '<ul class="list-categories-admin">';
            foreach ($query->result_array() as $key) {
                $content .= '<li><a href="#" id="'.$key['category_id'].'" class="setMainCategoryPos" title="">'.$key['translation_value'].'</a>';
                $content .= $this->getCategoriesTreeAdminAddNewCategory($key['category_id'], $level + 1, $lang, $url);
                $content .= '</li>';

            }
            $url = NULL;
            $content .= '</ul>';
            return $content;
        }
        else{
            return FALSE;
        }
    }

	public function getCategoryPath($catID, $level = 0){
		$query = $this->db->select(array('category_parent_id'))->where('category_id', $catID)->get($this->_categoriesTable);
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key) {
				$urlStr = $catID.'/';
				$urlStr .= $this->getCategoryPath($key['category_parent_id'], $level-1);
			}
			
			return $urlStr;

		}
		else{
			return NULL;
		}
	}

	public function getCategoryLink($str, $lang = 'pl'){
		$urlArray = array_reverse(array_values(array_filter(explode('/', $str))));
		$content = '';
		if(is_array($urlArray) AND !empty($urlArray)){

			$link = '';
			for ($i=0; $i <= count($urlArray)-1 ; $i++) {
				$url = $urlArray[$i];

				$whereArray = array(
					'translation_parent_id' 	=> $url,
					'translation_type' 			=> $this->_translationsType,
					'translation_lang' 			=> $lang,
					); 

				$query = $this->db->select('translation_url')->where($whereArray)->limit(1)->get($this->_translationsTable);
				if($query->num_rows() > 0){
					foreach ($query->result_array() as $key) {
						$content .= $key['translation_url'].'/';
					}
				}
			}
			return $content;
		}
		else{
			return NULL;
		}
	}

	/*	SPRAWDZANIE POPRAWNOŚCI UŁOŻENIA KATEGORII W URL PRODUKTU 	*/

	public function checkIfCategoryExistsSingle(Array $pathArray, $lang = 'pl'){
		$array = array();
		for ($i=0; $i <= count($pathArray)-1 ; $i++) { 
			$whereArray = array(
				'translation_url' 	=> $pathArray[$i],
				'translation_type' 	=> $this->_translationsType,
				'translation_lang' 	=> $lang,
				);
			$query = $this->db->select('*')
				->where($whereArray)
				->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
				->limit(1)
				->get($this->_categoriesTable);

			if($query->num_rows() > 0){
				$array[] = $query->result_array();
			}
		}
		return $array;
	}

	public function checkIfCategoryExists(Array $pathArray, $lang = 'pl'){
		if(is_array($pathArray) AND !empty($pathArray)){

			$array = array();
			for ($i=0; $i <= count($pathArray)-1 ; $i++) { 
				foreach ($pathArray[$i] as $key) {
					$array[] = array(
						'translation_url' 		=> $key['translation_url'],
						'translation_parent_id' => (int)$key['translation_parent_id'],
						'translation_type' 		=> $key['translation_type'],
						);
				}
			}
			$categoryArray 	= array();
			$productArray 	= array();

			for ($i=0; $i <= count($array)-1; $i++) { 
				if($array[$i]['translation_type'] === $this->_translationsType){
					$categoryArray[] = $array[$i];
				}
			}

			$categoryArray = array_reverse($categoryArray);
			return $this->Categories_model->checkCategoriesRelations($categoryArray, $lang);
			
		}
		else{
			return FALSE;
		}
	
	}

	public function checkCategoriesRelations(Array $categoriesArray, $lang = 'pl'){
		$queryArray = array();
		$positions = count($categoriesArray)-1;
		for ($i=0; $i <= count($categoriesArray)-2 ; $i++) { 
			$whereArray = array(
				'category_id' => $categoriesArray[$i]['translation_parent_id'],
				'category_parent_id' => $categoriesArray[$i+1]['translation_parent_id'],
				);
			$queryArray[] = (bool)$this->db->select('category_id')->where($whereArray)->limit(1)->get($this->_categoriesTable)->num_rows();
		}

		$whereArray = array(
				'category_id' => $categoriesArray[$positions]['translation_parent_id'],
				'category_parent_id' => 0,
				);
		$queryArray[] = (bool)$this->db->select('category_id')->where($whereArray)->limit(1)->get($this->_categoriesTable)->num_rows();
		if(in_array(FALSE, $queryArray)){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}

	/*	CRUD kategorii 		*/

	public function readCategoryBySlug($slug, $lang = 'pl'){
		$whereArray = array(
			'translation_type' 		=> $this->_translationsType,
			'translation_lang' 		=> $lang,
			'translation_url'		=> $slug,
			);
		$query = $this->db->select('*')
			->where($whereArray)
			->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
			->limit(1)
			->get($this->_categoriesTable);

		if($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return FALSE;
		}
	}

	public function readCategoryByID($categoryID, $lang = 'pl'){
		$whereArray = array(
			'translation_type' 		=> $this->_translationsType,
			'translation_lang' 		=> $lang,
			'category_id'			=> $categoryID,
			);
		$query = $this->db->select('*')
			->where($whereArray)
			->join($this->_translationsTable, $this->_translationsTable.'.translation_parent_id = '.$this->_categoriesTable.'.category_id')
			->limit(1)
			->get($this->_categoriesTable);

		if($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return FALSE;
		}
	}

	public function readCategoriesTranslations($limit, $offset){
		$this->load->model('languages_model');
		return $this->languages_model->readPaginatedTranslationsByType($this->_translationsType, $limit, $offset);
	}

	public function validateCreateCategory(){
		$this->form_validation->set_rules('parentID', $this->lang->line('choose_category_parent'), 'required|trim|htmlspecialchars|integer');
		$this->form_validation->set_rules('language', $this->lang->line('choose_language'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('value', $this->lang->line('set_value'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_title', $this->lang->line('set_seo_title'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_keywords', $this->lang->line('set_seo_keywords'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_description', $this->lang->line('set_seo_description'), 'required|trim|htmlspecialchars');

		if($this->form_validation->run() == TRUE){

			$this->load->model('languages_model');

			$parentID = $this->input->post('parentID', TRUE);
			$language = $this->input->post('language', TRUE);

			$parentCatArray = $this->languages_model->readTranslationByParentAndType($parentID, $this->_translationsType, $language);
			if(is_array($parentCatArray) AND !empty($parentCatArray)){

				$value = $this->input->post('value', TRUE);

				$insertTranslationArray = array(
					'translation_lang' 				=> $language,
					'translation_type' 				=> $this->_translationsType,
					'translation_value' 			=> $value,
					'translation_url_title' 		=> $value,
					'translation_url'				=> $this->_permalink($value),
					'translation_seo_title'			=> $this->input->post('seo_title', TRUE),
					'translation_seo_keywords'		=> $this->input->post('seo_keywords', TRUE),
					'translation_seo_description'	=> $this->input->post('seo_description', TRUE),
					'translation_cdate'				=> time(),
					);

				$result = $this->languages_model->createTranslationReturnID($insertTranslationArray);
				if($result !== FALSE AND is_numeric($result)){

					$categoryParentArray = $this->readCategoryByID($parentID, $language);
					if(is_array($categoryParentArray) AND !empty($categoryParentArray)){

						if($categoryParentArray[0]['category_parent_id'] > 0){
							$parent = $categoryParentArray[0]['category_id'];
							$level = $categoryParentArray[0]['category_level'] + 1;
						}
						else{
							$parent = 0;
							$level = 0;
						}

						$insertCategoryArray = array(
							'category_level' 			=> $level,
							'category_parent_id' 		=> $parent,
							'category_translations_id' 	=> $result,
							'category_cdate' 			=> time(),
							);

						$query = $this->db->insert($this->_categoriesTable, $insertCategoryArray);
						if($query == TRUE){
							$categoryID = $this->db->insert_id();

							$translationUpdateArray = array(
								'translation_parent_id' => $categoryID
								);

							$query = $this->languages_model->updateTranslationByID($result, $translationUpdateArray);
							if($query == TRUE){
								$this->session->set_flashdata('success', $this->lang->line('validate_category_create_success'));
							}
							else{
								$this->languages_model->deleteTranslationByID($result);
								$this->session->set_flashdata('error', $this->lang->line('validate_category_create_error'));
							}
							redirect($_SERVER['HTTP_REFERER'], 'refresh');
						}
						else{
							$this->session->set_flashdata('error', $this->lang->line('validate_category_no_created'));
							redirect($_SERVER['HTTP_REFERER'], 'refresh');
						}

					}
					else{
						$this->session->set_flashdata('error', $this->lang->line('validate_category_no_parent_category'));
						redirect($_SERVER['HTTP_REFERER'], 'refresh');
					}

				}
				else{
					$this->session->set_flashdata('error', $this->lang->line('validate_category_no_translation_created'));
					redirect($_SERVER['HTTP_REFERER'], 'refresh');
				}
			}
			else{
				$this->session->set_flashdata('error', $this->lang->line('validate_category_no_parent_translation'));
				redirect($_SERVER['HTTP_REFERER'], 'refresh');
			}
		}
		else{
			var_dump(validation_errors());
			die();
		}
	}

	public function validateUpdateCategory(){
		$this->form_validation->set_rules('parentID', $this->lang->line('choose_category_parent'), 'required|trim|htmlspecialchars|integer');
		$this->form_validation->set_rules('language', $this->lang->line('choose_language'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('value', $this->lang->line('set_value'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_title', $this->lang->line('set_seo_title'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_keywords', $this->lang->line('set_seo_keywords'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('seo_description', $this->lang->line('set_seo_description'), 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('categoryID', 'CID', 'required|trim|htmlspecialchars|integer|is_natural_no_zero');
		$this->form_validation->set_rules('translationID', 'TID', 'required|trim|htmlspecialchars|integer|is_natural_no_zero');

		if($this->form_validation->run() == TRUE){

			$this->load->model('languages_model');

			$parentID 			= $this->input->post('parentID', TRUE);
			$language 			= $this->input->post('language', TRUE);
			$categoryID 		= $this->input->post('categoryID', TRUE);
			$translationID 		= $this->input->post('translationID', TRUE);

			$parentCatArray = $this->languages_model->readTranslationByParentAndType($parentID, $this->_translationsType, $language);
			if(is_array($parentCatArray) AND !empty($parentCatArray)){

				$value = $this->input->post('value', TRUE);

				$updateTranslationArray = array(
					'translation_value' 			=> $value,
					'translation_url_title' 		=> $value,
					'translation_url'				=> $this->_permalink($value),
					'translation_seo_title'			=> $this->input->post('seo_title', TRUE),
					'translation_seo_keywords'		=> $this->input->post('seo_keywords', TRUE),
					'translation_seo_description'	=> $this->input->post('seo_description', TRUE),
					);

				$result = $this->languages_model->updateTranslationByID($translationID, $updateTranslationArray);
				if($result !== FALSE){

					$categoryParentArray = $this->readCategoryByID($parentID, $language);
					if(is_array($categoryParentArray) AND !empty($categoryParentArray)){

						if($categoryParentArray[0]['category_parent_id'] > 0){
							$parent = $categoryParentArray[0]['category_id'];
							$level = $categoryParentArray[0]['category_level'] + 1;
						}
						else{
							$parent = 0;
							$level = 0;
						}

						$updateCategoryArray = array(
							'category_level' 			=> $level,
							'category_parent_id' 		=> $parent,
							);

						$query = $this->db->where('category_id', $categoryID)->update($this->_categoriesTable, $updateCategoryArray);
						if($query == TRUE){
							$this->session->set_flashdata('success', $this->lang->line('validate_category_updated'));
							redirect($_SERVER['HTTP_REFERER'], 'refresh');
						}
						else{
							$this->session->set_flashdata('error', $this->lang->line('validate_category_no_updated'));
							redirect($_SERVER['HTTP_REFERER'], 'refresh');
						}

					}
					else{
						$this->session->set_flashdata('error', $this->lang->line('validate_category_no_parent_category'));
						redirect($_SERVER['HTTP_REFERER'], 'refresh');
					}

				}
				else{
					$this->session->set_flashdata('error', $this->lang->line('validate_category_no_translation_updated'));
					redirect($_SERVER['HTTP_REFERER'], 'refresh');
				}

			}
			else{
				$this->session->set_flashdata('error', $this->lang->line('validate_category_no_parent_translation'));
				redirect($_SERVER['HTTP_REFERER'], 'refresh');
			}

		}
		else{
			var_dump(validation_errors());
			die();
		}

	}


}
