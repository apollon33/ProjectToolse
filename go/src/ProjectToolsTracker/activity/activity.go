package activity

import (
	"gopkg.in/xkg.v0"
	"log"
	"github.com/BurntSushi/xgb/xproto"
	"github.com/BurntSushi/xgbutil/xgraphics"
	"github.com/BurntSushi/xgbutil"
	"github.com/BurntSushi/xgb"
	"github.com/go-vgo/robotgo"
	"time"
	"fmt"
	"os"
)

var KeyboardEvents int = 0
var MouseEvents int = 0
var KeyboardEventsFullStackPerMinute int = 30
var MouseEventsFullStackPerMinute int = 30

var ImgName string = "";
var Tracking bool = false

var ExpectedDelayTime int = 5 * 60 //seconds
var DelayTime int = 0 //seconds
var PreviewTime int = 10 //seconds

func Clear() {
	KeyboardEvents = 0;
	MouseEvents = 0;
	DelayTime = 0;
}

func CalculateForKeyboard() int {
	return calculateActivity(KeyboardEvents,KeyboardEventsFullStackPerMinute)
}
func CalculateForMouse() int {
	return calculateActivity(MouseEvents,MouseEventsFullStackPerMinute)
}

func calculateActivity(events int, fullStackEvents int) int {
	if (DelayTime == 0 || fullStackEvents == 0) {
		return 0
	}
	activity := int(100 * events * 60 / DelayTime / fullStackEvents);
	if (activity < 100) {
		return int(100 * events * 60 / DelayTime / fullStackEvents);
	} else {
		return 100;
	}
}


func WatchForKeyboard() {

	var keys = make(chan int, 100)
	keys <- 5;
	go xkg.StartXGrabber(keys)
	for {
		keycode := <-keys
		if _, ok := xkg.KeyMap[keycode]; ok {
			KeyboardEvents +=1;
		}
	}
}

func WatchForMouse() {

	for {
		mouseClick := robotgo.AddEvent("mleft")
		if mouseClick == 0 {
			MouseEvents +=1;
		}
	}
}

func CreateScreenShot() {
	X, err := xgbutil.NewConn()
	if err != nil {
		log.Fatal(err)
	}

	ximg, err := xgraphics.NewDrawable(X, xproto.Drawable(X.RootWin()))
	if err != nil {
		log.Fatal(err)
	}
	//ximg.XShowExtra("Screenshot", true)

	var randNumber =time.Now().Unix();
	ImgName = "scr-" + fmt.Sprint(randNumber)  + ".png";
	err = ximg.SavePng(ImgName)

	if err != nil {
		log.Fatal(err);
		ResetImageName();
	}
	//xevent.Main(X)
}

func ResetImageName() {
	ImgName = "";
}

func DeleteImageFile()  {
	if (ImgName != "") {
		path, _ := os.Getwd()
		path += "/" + ImgName
		var err = os.Remove(path)
		if err != nil {
			log.Fatal(err);
		}
		ResetImageName();
	}
}

func GetActiveWindow() string {
	X, err := xgb.NewConn()
	if err != nil {
		log.Fatal(err)
	}

	// Get the window id of the root window.
	setup := xproto.Setup(X)
	root := setup.DefaultScreen(X).Root

	// Get the atom id (i.e., intern an atom) of "_NET_ACTIVE_WINDOW".
	aname := "_NET_ACTIVE_WINDOW"
	activeAtom, err := xproto.InternAtom(X, true, uint16(len(aname)),
		aname).Reply()
	if err != nil {
		log.Fatal(err)
	}

	// Get the atom id (i.e., intern an atom) of "_NET_WM_NAME".
	aname = "_NET_WM_NAME"
	nameAtom, err := xproto.InternAtom(X, true, uint16(len(aname)),
		aname).Reply()
	if err != nil {
		log.Fatal(err)
	}

	// Get the actual value of _NET_ACTIVE_WINDOW.
	// Note that 'reply.Value' is just a slice of bytes, so we use an
	// XGB helper function, 'Get32', to pull an unsigned 32-bit integer out
	// of the byte slice. We then convert it to an X resource id so it can
	// be used to get the name of the window in the next GetProperty request.
	reply, err := xproto.GetProperty(X, false, root, activeAtom.Atom,
		xproto.GetPropertyTypeAny, 0, (1 << 32) - 1).Reply()
	if err != nil {
		log.Fatal(err)
	}
	windowId := xproto.Window(xgb.Get32(reply.Value))
	//fmt.Printf("Active window id: %X\n", windowId)

	// Now get the value of _NET_WM_NAME for the active window.
	// Note that this time, we simply convert the resulting byte slice,
	// reply.Value, to a string.
	reply, err = xproto.GetProperty(X, false, windowId, nameAtom.Atom,
		xproto.GetPropertyTypeAny, 0, (1 << 32) - 1).Reply()
	if err != nil {
		log.Fatal(err)
	}

	return string(reply.Value)
	//fmt.Printf("Active window name: %s\n", string(reply.Value))
}