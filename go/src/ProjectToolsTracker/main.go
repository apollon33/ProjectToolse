package main

import (
	"C"
	"fmt"

	"ProjectToolsTracker/activity"
	pthttp "ProjectToolsTracker/http"
	"github.com/mattn/go-gtk/gtk"
	//"github.com/gotk3/gotk3/gtk"
	//"github.com/mattn/go-gtk/gdk"
	"os"
	"time"
	"math/rand"

	"github.com/google/gxui"
	"github.com/google/gxui/themes/light"
	"github.com/google/gxui/gxfont"
	"github.com/google/gxui/drivers/gl"
	"github.com/google/gxui/math"
	"image"
	"image/draw"
	_ "image/jpeg"
	_ "image/png"

	"github.com/google/gxui/samples/flags"

	"strings"
	"encoding/json"
	"io/ioutil"
)

var project int = 0
var description string

var pointCenter = math.Point{800, 400}
var pointRightDown = math.Point{1800,1000}

var loginDataFileName = ".lng.dat"

func main() {

	gtk.Init(&os.Args)

		logged := login();

		if (logged == true && pthttp.Token != "") {
			fmt.Println("Login success.");

			projects := getProjects();

			go activity.WatchForKeyboard()
			go activity.WatchForMouse()

			showTrackForm(projects, &project, &description);
		}

}

func getProjects() []pthttp.Project {
	response:= pthttp.GetProjectsListRequest();

	if response.Status == false {
		fmt.Println("Error loading projects.")
	}
	if response.Status == true {
		if len(response.Projects) == 0 {
			fmt.Println("You do not have any active projects.")
		}
	}
	return response.Projects
}

func login() bool {

	//var username string
	//var password string

	loadLoginData(&pthttp.LoginData)

	for {
		formComlete := showLoginForm(&pthttp.LoginData)
		if (formComlete == false) {
			break
		}

		if validateLoginForm(&pthttp.LoginData) == false {
			continue
		}

		response, errorLogin := pthttp.SendLoginRequest(pthttp.LoginData);
		if errorLogin != nil {
			fmt.Printf("%#v\n", errorLogin.Error())
			pthttp.LoginData.Error = fmt.Sprint(errorLogin.Error())
			continue
		}
		if (response.Status == true) {
			if (response.Result["token"] != "") {
				pthttp.Token = response.Result["token"];
				saveLoginData(&pthttp.LoginData)
				return true
			}
		} else {
			pthttp.HandleRequestError(response);
		}
	}
	return false

}
func saveLoginData(loginData *pthttp.Authorization) {
	file, err := os.Create(loginDataFileName)
	if err != nil {
		// handle the error here
		return
	}
	defer file.Close()

	jsonLoginData, _ := json.Marshal((*loginData))
	file.WriteString(string(jsonLoginData))
}
func loadLoginData(loginData *pthttp.Authorization) {
	file, err := ioutil.ReadFile(loginDataFileName)
	if err != nil {
		// handle the error here
		return
	}
	jsonString := []byte(string(file))
	_ = json.Unmarshal(jsonString, &(*loginData))
}
func validateLoginForm(LoginData *pthttp.Authorization) bool {
	if (LoginData.Url == "") {
		LoginData.Error = "URL can not be empty"
		return false
	}
	if (strings.Index(LoginData.Url, "http://") == -1) {
		LoginData.Error = "Invalid URL. It must contain 'http://' "
		return false
	}
	if (LoginData.UserName == "") {
		LoginData.Error = "UserName can not be empty"
		return false
	}
	if (LoginData.Password == "") {
		LoginData.Error = "Password can not be empty"
		return false
	}
	LoginData.Error = ""
	return true
}

func trackActivity(stopTrackSignal chan bool , stopWaitSignal *chan bool, waitTime *chan int, previewSignal *chan bool, stopPreviewSignal *chan bool) {
	fmt.Println("Tracking started")
	for {
		activity.Clear();
		rand.Seed(time.Now().Unix())
		activity.DelayTime = int( activity.ExpectedDelayTime / 2 + rand.Intn(activity.ExpectedDelayTime) )

		for k := 0; k <= activity.DelayTime; k++ {
			select {
			default:
				time.Sleep(time.Second)
			case <- stopTrackSignal:
				return //end trackingActivity func

			}
		}

		//activity.CreateScreenShot()
		var keyboardActivity int = activity.CalculateForKeyboard();
		var mouseActivity int = activity.CalculateForMouse();

		if (keyboardActivity == 0 &&  mouseActivity == 0) {

			// send "no activity" time to "wait form"
			*waitTime <- activity.DelayTime
			// Expect when "wait form" closes
			<- *stopWaitSignal
		} else {
			// send "no signal" time to "preview form"
			*previewSignal <- true
			// Expect when "preview form" closes
			<- *stopPreviewSignal
		}
		//activity.DeleteImageFile()
	}
}


func showLoginForm(loginData *pthttp.Authorization) bool {
	var formResult bool = false

	gl.StartDriver(func (driver gxui.Driver) {

		theme := light.CreateTheme(driver)

		font, err := driver.CreateFont(gxfont.Default, 14)
		if err != nil {
			panic(err)
		}

		window := theme.CreateWindow(300, 300, "Connect to server")
		window.SetPadding(math.Spacing{30,30,30,30})

		window.SetPosition(pointCenter)

		layout := theme.CreateLinearLayout()
		layout.SetSizeMode(gxui.Fill)

		labelUrl := theme.CreateLabel()
		labelUrl.SetText("API Url:")
		labelUrl.SetFont(font)
		layout.AddChild(labelUrl)

		entryUrl := theme.CreateTextBox()
		//entryUrl.SetText("http://project-tools.loc/api") //remove this
		entryUrl.SetText((*loginData).Url)
		entryUrl.SetDesiredWidth(170)
		entryUrl.OnTextChanged(func(text []gxui.TextBoxEdit) {
			(*loginData).Url = entryUrl.Text()
		})
		layout.AddChild(entryUrl)

		labelUserName := theme.CreateLabel()
		labelUserName.SetText("UserName:")
		labelUserName.SetFont(font)
		layout.AddChild(labelUserName)

		entryUserName := theme.CreateTextBox()
		entryUserName.SetText((*loginData).UserName)
		entryUserName.SetDesiredWidth(170)
		entryUserName.OnTextChanged(func(text []gxui.TextBoxEdit) {
			(*loginData).UserName = entryUserName.Text()
		})
		layout.AddChild(entryUserName)

		labelPassword := theme.CreateLabel()
		labelPassword.SetText("Password:")
		labelPassword.SetFont(font)
		layout.AddChild(labelPassword)

		entryPassword := theme.CreateTextBox()
		entryPassword.SetText(hiddenPasswordGenerator((*loginData).Password))
		entryPassword.SetDesiredWidth(170)
		entryPassword.OnGainedFocus(func() {
			entryPassword.SetText("")
		})
		entryPassword.OnLostFocus(func() {
			(*loginData).Password = entryPassword.Text()
			entryPassword.SetText(hiddenPasswordGenerator((*loginData).Password))
		})

		layout.AddChild(entryPassword)

		if (*loginData).Error != "" {

			showErrorForm(&driver, &window, (*loginData).Error)

		}

		buttonConfirm := theme.CreateButton()
		buttonConfirm.SetText("Connect")
		buttonConfirm.SetPadding(math.Spacing{32, 2, 32, 2})
		buttonConfirm.OnClick(func(gxui.MouseEvent) {
			driver.Terminate()
			formResult = true
		})

		layout.AddChild(buttonConfirm)

		layout.SetHorizontalAlignment(gxui.AlignCenter)
		window.AddChild(layout)
		window.OnClose(driver.Terminate)
	})

	return formResult

}
func showErrorForm(driver *gxui.Driver, window *gxui.Window, error string) {
	(*driver).Call(func() {
		(*window).Hide()
		theme := light.CreateTheme(*driver)

		font, err := (*driver).CreateFont(gxfont.Default, 14)
		if err != nil {
			panic(err)
		}

		ErrorWindow := theme.CreateWindow(500, 100, "Error")
		ErrorWindow.SetPadding(math.Spacing{0,20,0,0})
		ErrorWindow.SetPosition(pointCenter)
		ErrorLayout := theme.CreateLinearLayout()
		ErrorLayout.SetSizeMode(gxui.Fill)
		labelError := theme.CreateLabel()
		labelError.SetText(error)
		labelError.SetFont(font)
		labelError.SetMultiline(true)
		labelError.SetHorizontalAlignment(gxui.AlignLeft)
		labelError.SetColor(gxui.Red60)
		ErrorLayout.AddChild(labelError)
		ErrorLayout.SetHorizontalAlignment(gxui.AlignCenter)
		ErrorWindow.AddChild(ErrorLayout)
		ErrorWindow.OnClose(func() {
			(*window).Show()
			(*window).SetPosition(pointCenter)
		})
	})
}

func showTrackForm(projects []pthttp.Project, project *int, description *string) {
	var projectSelecting bool = false

	gl.StartDriver(func (driver gxui.Driver) {
		theme := light.CreateTheme(driver)

		font, err := driver.CreateFont(gxfont.Default, 14)
		if err != nil {
			panic(err)
		}

		// === TRACK WINDOW ===

		windowTrack := theme.CreateWindow(300, 200, "ProjectToolsTracker")
		windowTrack.SetPosition(pointRightDown)
		windowTrack.SetPadding(math.Spacing{0,30,0,0})

		layoutTrack := theme.CreateLinearLayout()
		layoutTrack.SetSizeMode(gxui.Fill)

		labelProject := theme.CreateLabel()
		labelProject.SetText("Project:")
		labelProject.SetFont(font)
		layoutTrack.AddChild(labelProject)

		buttonProjects := theme.CreateButton()
		buttonProjects.SetText("Select")
		buttonProjects.SetPadding(math.Spacing{32, 2, 32, 2})
		buttonProjects.OnClick(func(gxui.MouseEvent) {
			if projectSelecting == false {
				showProjectForm(&driver, projects, &buttonProjects)
				//projectSelecting = true
			}
		})
		layoutTrack.AddChild(buttonProjects)

		labelDescription := theme.CreateLabel()
		labelDescription.SetText("Description:")
		labelDescription.SetFont(font)
		layoutTrack.AddChild(labelDescription)

		entryDescription := theme.CreateTextBox()
		entryDescription.SetText("")
		entryDescription.SetDesiredWidth(170)
		entryDescription.OnSelectionChanged(func() {
			*description = entryDescription.Text()
		})
		layoutTrack.AddChild(entryDescription)

		labelProjectStatus := theme.CreateLabel()
		labelProjectStatus.SetText("Status:Stopped")
		labelProjectStatus.SetColor(gxui.Red60)
		layoutTrack.AddChild(labelProjectStatus)

		buttonStart := theme.CreateButton()
		buttonStart.SetText("Start")
		buttonStart.SetPadding(math.Spacing{32, 2, 32, 2})

		stopTrackSignal := make(chan bool)
		stopWaitSignal := make(chan bool)
		waitTime := make(chan int)
		previewSignal := make(chan bool)
		stopPreviewSignal := make(chan bool)

		buttonStart.OnClick(func(gxui.MouseEvent) {
			if activity.Tracking == true {
				activity.Tracking = false
				buttonStart.SetText("Start")
				labelProjectStatus.SetText("Status: Stopped")
				labelProjectStatus.SetColor(gxui.Red60)
				buttonStart.SetChecked(true)

				stopTrackSignal <- true
			} else {
				activity.Tracking = true
				buttonStart.SetText("Stop")
				labelProjectStatus.SetText("Status: Tracking")
				labelProjectStatus.SetColor(gxui.Green60)
				buttonStart.SetChecked(false)

				go trackActivity(stopTrackSignal, &stopWaitSignal, &waitTime, &previewSignal, &stopPreviewSignal)

				//showWaitForm(&driver)
			}
		})
		layoutTrack.AddChild(buttonStart)

		layoutTrack.SetHorizontalAlignment(gxui.AlignCenter)

		windowTrack.AddChild(layoutTrack)

		windowTrack.OnClose(func() {
			driver.Terminate()
		})

		go func() {
			for {
				//global form event listener
				select {
				default:
				case waitTimeReceiver :=<- waitTime:
					driver.Call(func() {
						showWaitForm(&driver, &stopWaitSignal, waitTimeReceiver)
					})
				case <- previewSignal:
					driver.Call(func() {
						showPreviewForm(&driver, &stopPreviewSignal)
					})

				}
				time.Sleep(time.Second * 1)
			}
		}()

	})

}

func showProjectForm(driver *gxui.Driver, projects []pthttp.Project, buttonProjects *gxui.Button) {
	theme := light.CreateTheme(*driver)

	// === Project Window ===
	// Dropdown conflicts with other elements, so we need to user new window

	windowProject := theme.CreateWindow(150, 150, "Projects")
	windowProject.SetPosition(pointCenter)
	windowProject.SetPadding(math.Spacing{0,0,0,0})
	layoutProject := theme.CreateLinearLayout()
	layoutProject.SetSizeMode(gxui.Fill)

	buttonProjectEmpty := theme.CreateButton()
	buttonProjectEmpty.SetText("Empty")
	buttonProjectEmpty.SetPadding(math.Spacing{32, 2, 32, 2})
	buttonProjectEmpty.OnClick(func(gxui.MouseEvent) {
		project = 0
		(*buttonProjects).SetText("Empty")
		windowProject.Close()
	})
	layoutProject.AddChild(buttonProjectEmpty)

	adapter := gxui.CreateDefaultAdapter()
	adapter.SetItems(projects)

	projectCounter := 0
	adapter.SetStyleLabel(func(theme gxui.Theme, label gxui.Label) {
		label.SetText(projects[projectCounter].Name)
		projectCounter++
	})

	dropDownProjectList := theme.CreateList()
	dropDownProjectList.SetAdapter(adapter)
	dropDownProjectList.OnSelectionChanged(func(sel gxui.AdapterItem) {
		projectItem := sel.(pthttp.Project)
		project = projectItem.Id
		(*buttonProjects).SetText(projectItem.Name)
		windowProject.Close()
	})

	layoutProject.AddChild(dropDownProjectList)
	layoutProject.SetHorizontalAlignment(gxui.AlignCenter)
	windowProject.AddChild(layoutProject)
}

func showWaitForm(driver *gxui.Driver, stopWaitSignal *chan bool, waitTime int) {
	// === WAIT WINDOW ===

	var formOpened bool = true;

	theme := light.CreateTheme(*driver)

	font, err := (*driver).CreateFont(gxfont.Default, 14)
	if err != nil {
		panic(err)
	}

	windowWait := theme.CreateWindow(300, 150, "No activity")
	windowWait.SetPosition(pointCenter)
	windowWait.SetPadding(math.Spacing{0,30,0,0})

	layoutWait := theme.CreateLinearLayout()
	layoutWait.SetSizeMode(gxui.Fill)

	labelWaitTitle := theme.CreateLabel()
	labelWaitTitle.SetText("Missing for:")
	labelWaitTitle.SetFont(font)
	layoutWait.AddChild(labelWaitTitle)

	labelTimeOut := theme.CreateLabel()
	labelTimeOut.SetText("0 sec")
	labelTimeOut.SetFont(font)
	layoutWait.AddChild(labelTimeOut)

	entryWaitDescription := theme.CreateTextBox()
	entryWaitDescription.SetText("")
	entryWaitDescription.SetDesiredWidth(170)
	layoutWait.AddChild(entryWaitDescription)

	buttonOk := theme.CreateButton()
	buttonSkip := theme.CreateButton()
	buttonOk.SetText("Ok")
	buttonSkip.SetText("Skip")
	buttonOk.SetPadding(math.Spacing{32, 2, 32, 2})
	buttonSkip.SetPadding(math.Spacing{32, 2, 32, 2})

	buttonOk.OnClick(func(gxui.MouseEvent) {
		windowWait.Close()

		activity.CreateScreenShot()
		pthttp.SendActivityCreateRequest(waitTime, project, 0, 0, activity.GetActiveWindow(), entryWaitDescription.Text());

		fmt.Printf("- Time: %d sec.\nNon activity data sent.",
			waitTime);
		fmt.Println();

	})

	buttonSkip.OnClick(func(gxui.MouseEvent) {
		windowWait.Close()
	})

	layoutWaitButtons := theme.CreateLinearLayout()

	layoutWaitButtons.AddChild(buttonOk)
	layoutWaitButtons.AddChild(buttonSkip)
	layoutWaitButtons.SetDirection(gxui.LeftToRight)
	layoutWait.AddChild(layoutWaitButtons)

	layoutWait.SetHorizontalAlignment(gxui.AlignCenter)

	windowWait.AddChild(layoutWait)

	windowWait.OnClose(func() {
		formOpened = false
		*stopWaitSignal <- true
		activity.DeleteImageFile()
	})

	go func() {
		for formOpened {
			(*driver).Call(func() {
				labelTimeOut.SetText(getTimerSting(waitTime))
			})
			waitTime++
			time.Sleep(time.Second * 1)
		}
	}()

}

func showPreviewForm(driver *gxui.Driver, stopPreviewSignal *chan bool) {
	// === Preview WINDOW ===

	var allowSending bool = true;
	var formOpened bool = true;
	var activeWindow string = activity.GetActiveWindow();
	activity.CreateScreenShot()

	theme := light.CreateTheme(*driver)

	font, err := (*driver).CreateFont(gxfont.Default, 14)
	if err != nil {
		panic(err)
	}

	windowPreview := theme.CreateWindow(200, 200, "Screen preview")
	windowPreview.SetPosition(pointRightDown)
	windowPreview.SetPadding(math.Spacing{0,0,0,0})

	layoutPreview := theme.CreateLinearLayout()
	layoutPreview.SetSizeMode(gxui.Fill)

	labelPreview := theme.CreateLabel()
	labelPreview.SetText(activeWindow)
	labelPreview.SetFont(font)
	layoutPreview.AddChild(labelPreview)

	file := activity.ImgName
	f, err := os.Open(file)

	source, _, err := image.Decode(f)
	if err != nil {
		fmt.Printf("Failed to read image '%s': %v\n", file, err)
		os.Exit(1)
	}

	imgPreview := theme.CreateImage()
	imgPreview.SetExplicitSize(math.Size{192,108})
	imgPreview.SetScalingMode(gxui.ScalingExplicitSize)
	layoutPreview.AddChild(imgPreview)


	rgba := image.NewRGBA(source.Bounds())
	draw.Draw(rgba, source.Bounds(), source, image.ZP, draw.Src)
	texture := (*driver).CreateTexture(rgba, 1)
	imgPreview.SetTexture(texture)

	labelTimeLeft := theme.CreateLabel()
	labelTimeLeft.SetText("")
	labelTimeLeft.SetFont(font)
	layoutPreview.AddChild(labelTimeLeft)

	buttonDelete := theme.CreateButton()
	buttonDelete.SetText("Delete")
	buttonDelete.SetPadding(math.Spacing{32, 2, 32, 2})

	buttonDelete.OnClick(func(gxui.MouseEvent) {
		allowSending = false
		windowPreview.Close()
	})
	layoutPreview.AddChild(buttonDelete)

	layoutPreview.SetHorizontalAlignment(gxui.AlignCenter)
	windowPreview.AddChild(layoutPreview)

	windowPreview.SetScale(flags.DefaultScaleFactor)

	windowPreview.OnClose(func() {

		formOpened = false
		*stopPreviewSignal <- true

		if allowSending {
			var keyboardActivity int = activity.CalculateForKeyboard();
			var mouseActivity int = activity.CalculateForMouse();

			pthttp.SendActivityCreateRequest(activity.DelayTime + activity.PreviewTime, project, keyboardActivity, mouseActivity, activeWindow, description);

			fmt.Printf("- Events: %d/%d.\n- Percent: %d/%d.\n- Time: %d sec.\nActivity data sent.",
				activity.KeyboardEvents,
				activity.MouseEvents,
				keyboardActivity,
				mouseActivity, activity.DelayTime);
			fmt.Println();
		}

		activity.DeleteImageFile()
	})

	go func() {
		for timeOut := activity.PreviewTime; formOpened && timeOut > 0; timeOut--  {
			(*driver).Call(func() {
				labelTimeLeft.SetText("Time left: " + fmt.Sprint(timeOut) + " sec")
			})
			time.Sleep(time.Second)
		}
		if formOpened {
			(*driver).Call(func() {
				windowPreview.Close()
			})
		}
	}()
}

func getTimerSting(i int) string {
	sec := i % 60
	min := (i - sec) / 60

	if (i < 60) {
		return fmt.Sprint(sec) + " sec"
	} else {
		return fmt.Sprint(min) + " min " + fmt.Sprint(sec) + " sec"
	}
}

func hiddenPasswordGenerator(password string) string {
	hiddenString := ""
	for i:=0; i < len(password); i++ {
		hiddenString += "*"
	}
	return hiddenString
}