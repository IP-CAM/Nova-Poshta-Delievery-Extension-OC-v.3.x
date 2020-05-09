<?php

class ControllerShippingOcnpNovaposhta extends Controller
{
   private $m_data = array();

   private function loadResources()
   {
      $this->load->language('shipping/ocnp_novaposhta');
      $this->document->setTitle($this->language->get('heading_title'));
      $this->m_data['heading_title'] = $this->language->get('heading_title');
      $this->m_data['text_edit'] = $this->language->get('text_edit');
      $this->m_data['text_enabled'] = $this->language->get('text_enabled');
      $this->m_data['text_disabled'] = $this->language->get('text_disabled');
      $this->m_data['text_select'] = $this->language->get('text_select');
      $this->m_data['entry_status'] = $this->language->get('entry_status');
      $this->m_data['ocnp_entry_api_url'] = $this->language->get('entry_api_url');
      $this->m_data['ocnp_entry_api_key'] = $this->language->get('entry_api_key');
      $this->m_data['ocnp_entry_city_from'] = $this->language->get('entry_city_from');
      $this->m_data['ocnp_entry_sort_order'] = $this->language->get('entry_sort_order');
      $this->m_data['ocnp_entry_min_total_for_free_delivery'] = $this->language->get('entry_min_total_for_free_delivery');
      $this->m_data['button_save'] = $this->language->get('button_save');
      $this->m_data['button_cancel'] = $this->language->get('button_cancel');
      $this->m_data['admin_language_id'] = $this->config->get('config_admin_language');
   }

   private function setBreadcrumbs()
   {
      $this->document->breadcrumbs = array();

      $this->document->breadcrumbs[] = array(
         'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
         'text' => $this->language->get('text_home'),
         'separator' => FALSE
      );

      $this->document->breadcrumbs[] = array(
         'href' => HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token'],
         'text' => $this->language->get('text_shipping'),
         'separator' => ' :: '
      );

      $this->document->breadcrumbs[] = array(
         'href' => HTTPS_SERVER . 'index.php?route=shipping/ocnp_novaposhta&token=' . $this->session->data['token'],
         'text' => $this->language->get('heading_title'),
         'separator' => ' :: '
      );
   }

   private function loadSettings()
   {
      $settings = array(
         'ocnp_novaposhta_min_total_for_free_delivery',
         'ocnp_novaposhta_status',
         'ocnp_novaposhta_api_url',
         'ocnp_novaposhta_api_key',
         'ocnp_novaposhta_city_from',
         'ocnp_novaposhta_sort_order'
      );

      foreach($settings as $setting)
      {
         if (isset($this->request->post[$setting]))
         {
            $this->m_data[$setting] = $this->request->post[$setting];
         }
         else
         {
            $this->m_data[$setting] = $this->config->get($setting);
         }
      }
   }

   public function index()
   {
      $this->loadResources();
      if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate()))
      {
         $this->load->model('setting/setting');
         $this->model_setting_setting->editSetting('ocnp_novaposhta', $this->request->post);
         $this->session->data['success'] = $this->language->get('text_success');
         $this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token']);
      }
      else
      {
         $this->setBreadcrumbs();
         $this->loadSettings();
         $this->m_data['action'] = HTTPS_SERVER . 'index.php?route=shipping/novaposhta&token=' . $this->session->data['token'];
         $this->m_data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token'];

         $this->load->model('shipping/ocnp_novaposhta');
         $this->m_data['cities'] = $this->model_localisation_novaposhta->getCitiesFromApi();

         $this->m_data['header'] = $this->load->controller('common/header');
         $this->m_data['column_left'] = $this->load->controller('common/column_left');
         $this->m_data['footer'] = $this->load->controller('common/footer');

         $this->response->setOutput($this->load->view('shipping/ocnp_novaposhta', $this->m_data));
      }
   }

   private function validate()
   {
      $IsValid = true;

      if (!$this->user->hasPermission('modify', 'shipping/ocnp_novaposhta'))
      {
         $this->m_data['warning'] = $this->language->get('error_permission');
         $IsValid = false;
      }
      else
      {
         $RequiredFields = array(
            'ocnp_novaposhta_min_total_for_free_delivery',
            'ocnp_novaposhta_api_url',
            'ocnp_novaposhta_api_key',
            'ocnp_novaposhta_city_from'
         );
   
         foreach($RequiredFields as $field)
         {
            if (!$this->request->post[$field])
            {
               $error = 'error_'.$field;
               $this->m_data[$error] = $this->language->get($error);
               $IsValid = false;
            }
         }
      }

      return $IsValid;
   }
}