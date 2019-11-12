<?php
/**
	ver 1.0.0.1
	rev 12.11.2019
	anatoliy.iwanov@yandex.ru
*/
class YourController extends Controller{
	
	private $error = array();
	private $extension_type = "module";
	private $extension_code = "code";
	private $extension_path = "p/a/t/h/"; #need to end with '/'
	private $url_path = "";
	
	public function index(){
		
		$data = array();
		$load_path = $this->extension_path . $this->extension_code;
		
		$this->load->language($load_path);
        $this->load->model($load_path);
        $this->load->model("setting/setting");
		
		$this->document->setTitle($this->language->get("heading_title"));
		
		if (($this->request->server["REQUEST_METHOD"] == "POST") && $this->validate()) {

            $this->model_setting_setting->editSetting($this->extension_code, $this->request->post);
            $this->session->data["success"] = $this->language->get("text_success");
            $this->response->redirect($this->url->link($this->extension_code, 
				'user_token=' . $this->session->data['user_token'] . '&type=' . $this->extension_type, true));
        }
		
		$data["error_warning"] = "";
        if (isset($this->error["warning"])) {
            $data["error_warning"] = $this->error["warning"];
        }
		
		$data[$this->extension_code . "_status"] = "";
        if (isset($this->request->post["mssync_status"])) {
            $data[$this->extension_code . "_status"] = $this->request->post[$this->extension_code . "_status"];
        } else {
            $data[$this->extension_code . "_status"] = $this->config->get($this->extension_code . "_status");
        }
		
		$data["breadcrumbs"] = array();

        $data["breadcrumbs"][] = array(
            "text" => $this->language->get("text_home"),
            "href" => $this->url->link("common/dashboard", "user_token=" . $this->session->data["user_token"], true)
        );

        $data["breadcrumbs"][] = array(
            "text" => $this->language->get("text_extension"),
            "href" => $this->url->link($this->url_path, "user_token=" . $this->session->data["user_token"] . "&type=" . $this->extension_type, true)
        );

        $data["breadcrumbs"][] = array(
            "text" => $this->language->get("heading_title"),
            "href" => $this->url->link($this->extension_path . $this->extension_code, "user_token=" . $this->session->data["user_token"], true)
        );
		
		$data["action"] = $this->url->link($this->extension_path . $this->extension_code, "user_token=" . $this->session->data["user_token"], true);
        $data["cancel"] = $this->url->link($this->url_path, "user_token=" . $this->session->data["user_token"] . "&type=" . $this->extension_type, true);
		
		$data["heading_title"] = $this->language->get("heading_title");
        $data["button_save"] = $this->language->get("button_save");
        $data["button_cancel"] = $this->language->get("button_cancel");
		$data["entry_status"] = $this->language->get("entry_status");
        $data["text_enabled"] = $this->language->get("text_enabled");
        $data["text_disabled"] = $this->language->get("text_disabled");
		
		$data["header"] = $this->load->controller("common/header");
        $data["column_left"] = $this->load->controller("common/column_left");
        $data["footer"] = $this->load->controller("common/footer");
		
		$this->response->setOutput($this->load->view($this->extension_path . $this->extension_code, $data));
		
	}
	
	/**
	ver 1.0.0.1
	rev 12.11.2019
	anatoliy.iwanov@yandex.ru
	*/
	//poprietary
    protected function validate() {

        if (!$this->user->hasPermission('modify', $this->extension_path . $this->extension_code)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
