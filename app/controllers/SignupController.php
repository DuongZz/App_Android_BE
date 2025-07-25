<?php
/**
 * Signup Controller
 */
class SignupController extends Controller
{
    /**
     * Process
     */
    public function process()
    {
        /**If AuthUser exists, print message */
        $AuthUser = $this->getVariable("AuthUser");
        if ($AuthUser) {
            $this->resp->result = 1;
            $this->resp->msg = __("You have been logging in !");
            $this->jsonecho();
        }

        /** */
        $this->signup();
    }

    /**
     * Signup
     * @return void
     */
    private function signup()
    {
        /**Step 0 - declare required_fields */
        $this->resp->result = 0;
        
        $required_fields  = array(
            "email", 
            "password", "password-confirm"
        );

        foreach ($required_fields as $field) 
        {
            if (!Input::post($field)) {
                $this->resp->msg = __("Missing field: ".$field);
                $this->jsonecho();
            }
        }

        /** Step 1 - declare variable to store value that HTTP sends us */
        $email = strtolower(Input::post("email"));
        $password = Input::post("password");
        $firstName = Input::post("first_name") ? Input::post("first_name") : Input::post("email");
        $lastName = Input::post("last_name") ? Input::post("last_name") : "";

        /**Step 3 - filter email */
        if (!filter_var(Input::post("email"), FILTER_VALIDATE_EMAIL)) {
            $this->resp->msg = __("Email is not valid!");
            $this->jsonecho();
        } 
        else 
        {
            $User = Controller::model("User", Input::post("email"));
            if ($User->isAvailable()) {
                $this->resp->msg = __("This email is used by someone!");
                $this->jsonecho();
            }
        }

        if (mb_strlen(Input::post("password")) < 6) {
            $this->resp->msg = __("Password must be at least 6 character length!");
            $this->jsonecho();
        } else if (Input::post("password-confirm") != Input::post("password")) {
            $this->resp->msg = __("Password confirmation didn't match!");
            $this->jsonecho();
        }

        /**Step 3.3 - check name - only letters and space */
        $first_name = Input::post("first_name");
        $first_name_validation = isVietnameseName($first_name);
        if( $first_name_validation != 1 ){
            $this->resp->msg = "First name only has letters and space";
            $this->jsonecho();
        }

        $last_name = Input::post("last_name");
        $last_name_validation = isVietnameseName($last_name);
        if( $last_name_validation != 1 ){
            $this->resp->msg = "Last name only has letters and space";
            $this->jsonecho();
        }

        try 
        {        
            $User->set("email", $email)
                ->set("password", password_hash($password, PASSWORD_DEFAULT))
                ->set("first_name", $firstName)
                ->set("last_name", $lastName)
                // ->set("phone", "") // ✘ Gây lỗi vì không có trong DB
                // ->set("address","Vietnam") // ✘ Gây lỗi nếu không có trong DB
                ->set("role", "member")
                ->set("active", 1)
                ->set("create_at", date("Y-m-d H:i:s"))
                ->set("update_at", date("Y-m-d H:i:s"))
                ->save();

            $data = array(
                "id"    => (int)$User->get("id"),
                "email" => $User->get("email"),
                "first_name" => $User->get("first_name"),
                "last_name" => $User->get("last_name"),
                "role" => $User->get("role"),
                "active" => $User->get("active"),
                "create_at" => $User->get("create_at"),
                "update_at" => $User->get("update_at")
            );

            $payload = $data;
            $payload["hashPass"] = md5($User->get("password"));
            $payload["iat"] = time();

            $jwt = Firebase\JWT\JWT::encode($payload, EC_SALT, 'HS256');

            $this->resp->result = 1;
            $this->resp->msg = __("Your account has been created successfully!");
            $this->resp->accessToken = $jwt;
            $this->resp->data = $data;

        } 
        catch (\Exception $ex) 
        {
            $this->resp->msg = $ex->getMessage();
        }
        $this->jsonecho();
    }
}
