<?php
class PersonalTimeTable Extends Controller {

    private $ticketsBoughtByUser = array();
    private $foodTickets = array();
    private $danceTickets = array();
    private $jazzTickets = array();
    private $historicTickets = array();
    private $kidsTickets = array();

    public function __construct() {
        //Sees if the session user id is there to check if you are logged in (in able to only access the page)
       if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->personalTimeTableDal = $this->dal('PersonalTimeTableDAO');
        $this->personalTimeTableModel = $this->model('PersonalTimeTableModel');
        $this->ticketsBoughtByUser = $this->personalTimeTableDal->getTicketsBoughtByUser();
//      $this->danceBll = $this->bll('Dance');
        $this->jazzBll = $this->bll('Jazz');
        $this->foodBll = $this->bll('Food');
        $this->setTimetableData();
    }

    public function setTimetableData(){
        foreach ($this->ticketsBoughtByUser as $ticket){
            array_push($this->jazzTickets, $this->jazzBll->getJazzTicketFromTicket($ticket['ticketId'], $ticket['reserved'], $ticket['start'], $ticket['end']));
            //array_push($this->danceTickets, $this->danceBll->getDanceTicketFromTicket($ticket['ticketId'], $ticket['reserved'], $ticket['start'], $ticket['end']));
        }
    }
    public function getTicketByDayAndTime($day, $time){
        foreach ($this->jazzTickets as $jazzTicket){

            $jazzTicketDayAndTime = explode(" ", $jazzTicket->getStartDateTime());
            $jazzTicketDay = $jazzTicketDayAndTime[0];
            $jazzTicketTime = $jazzTicketDayAndTime[1];

            if(($jazzTicketDay == $day) && ($jazzTicketTime == $time)){
                $artists = "";
                foreach($jazzTicket->getArtists() as $artist){
                    $artists .= $artist->getArtistName();
                }
                return $artists . " @ " . $jazzTicket->getJazzTicketLocation();
            }
        }
    }

    public function index () {
        //$days = $this->personalTimeTableDal->getDifferentDays();
        //$tickets = $this->personalTimeTableDal->getUserTickets($id = '1');

        $data = [
            'jazzTickets' => $this->jazzTickets,
            'danceTickets' => $this->danceTickets
        ];

      $this->ui('pages/personaltimetable', $data);
    }

}
