package http

import (
	"fmt"
	"net/http"
	"mime/multipart"
	"bytes"
	"io/ioutil"
	"os"
	"log"
	"encoding/json"
	"strings"
	//"errors"
	"ProjectToolsTracker/activity"
)

type Response struct {
	Status bool
	Result map[string]string
	Projects [] Project
	Errors map[string][]string
}

type Project struct {
	Id int
	Name string
}

type Authorization struct {
	Url string
	UserName string
	Password string
	Error string
}

var LoginData Authorization

var Token string
//var ApiUrl string

func HandleRequestError(response Response) {
	for key, errorArray := range response.Errors {
		for _, errorMessage := range errorArray {
			fmt.Println(key + " : " + errorMessage)
			LoginData.Error = fmt.Sprint(key + " : " + errorMessage)
		}
	}

	if len(response.Errors) == 0 {
		fmt.Println("Unknown error.")
		LoginData.Error = "Unknown error."
	}
}

func SendLoginRequest(loginData Authorization) (Response, error) {
	//url := ApiUrl + "/user/auth"
	url := loginData.Url + "/user/auth"
	jsonStr := strings.NewReader(`{
	"username":"` + loginData.UserName + `",
	"password":"` + loginData.Password + `"
	}`)
	req, _ := http.NewRequest("POST", url, jsonStr);
	req.Header.Add("content-type", "application/json");
	req.Header.Add("cache-control", "no-cache");
	res, err := http.DefaultClient.Do(req);

	var response Response
	if  err != nil {
		return response, err
	} else {
		defer res.Body.Close();
		body, _ := ioutil.ReadAll(res.Body);
		bodyJson := []byte(body);
		json.Unmarshal(bodyJson, &response);
		return response, nil
	}
}

func GetProjectsListRequest() Response {
	url := LoginData.Url + "/project"
	jsonStr := strings.NewReader(``)
	req, _ := http.NewRequest("GET", url, jsonStr);
	req.Header.Add("content-type", "application/json");
	req.Header.Add("cache-control", "no-cache");
	req.Header.Add("Authorization", "Bearer " + Token)
	res, err := http.DefaultClient.Do(req);

	var response Response
	if  err != nil {
		return response
	} else {
		defer res.Body.Close();
		body, _ := ioutil.ReadAll(res.Body);
		bodyJson := []byte(body);
		json.Unmarshal(bodyJson, &response);
		return response
	}
}

func SendActivityCreateRequest(
	interval int,
	projectId int,
	keyboardActivityPercent int,
	mouseActivityPercent int,
	targetWindow string,
	description string) Response {
	path, _ := os.Getwd()
	path += "/" + activity.ImgName

	projectIdString := "";
	if projectId != 0 {
		projectIdString = fmt.Sprintf("%d", projectId);
	}
	extraParams := map[string]string{
		"interval": fmt.Sprintf("%d", interval),
		"project_id": projectIdString,
		"keyboard_activity_percent": fmt.Sprintf("%d", keyboardActivityPercent),
		"mouse_activity_percent": fmt.Sprintf("%d", mouseActivityPercent),
		"target_window": targetWindow,
		"description": description,
	}

	url := LoginData.Url + "/activity"

	request, err := requestWithFileUpload(url, extraParams, "screenshot", path)
	if err != nil {
		log.Fatal(err)
	}

	//activity.DeleteImageFile();

	client := &http.Client{}
	resp, err := client.Do(request)

	defer resp.Body.Close();
	body, _ := ioutil.ReadAll(resp.Body);

	var response Response
	bodyJson := []byte(body);
	json.Unmarshal(bodyJson, &response);
	return response

}

func requestWithFileUpload(uri string, params map[string]string, paramName, path string) (*http.Request, error) {
	file, err := os.Open(path)
	if err != nil {
		return nil, err
	}
	fileContents, err := ioutil.ReadAll(file)
	if err != nil {
		return nil, err
	}
	fi, err := file.Stat()
	if err != nil {
		return nil, err
	}
	file.Close()

	body := new(bytes.Buffer)
	writer := multipart.NewWriter(body)
	part, err := writer.CreateFormFile(paramName, fi.Name())
	if err != nil {
		return nil, err
	}
	part.Write(fileContents)

	for key, val := range params {
		_ = writer.WriteField(key, val)
	}
	err = writer.Close()
	if err != nil {
		return nil, err
	}

	request, err := http.NewRequest("POST", uri, body)

	request.Header.Add("Content-Type", writer.FormDataContentType())
	request.Header.Add("Authorization", "Bearer " + Token)

	return request, err
}