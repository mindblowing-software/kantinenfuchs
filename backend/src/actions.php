<?php

namespace kfx;
use \Date;

class actions
{
    public $hasura;


    public function __construct($hasura)
    {
        $this->hasura = $hasura;
    }

    public function get_hello()
    {
        // print_r($this->hasura);
        return '** hello actions **';
    }

    public function get_free_slots($data) {

        // everything is fine
        $rc = true;
        $idx = 0;  //index for slots array
        $slots = array();

        dbg('++++ Input', $data['input']);    
        $behandlung_art_id = $data['input']['behandlungArtId'];
        $dauer = $data['input']['dauer'];
        $datum = $data['input']['datum'];
        $wochentag = getdate(strtotime($datum))['wday'];

        $resp = post_graphql($this->hasura, 'get_raeume_behandlungen_buchungen_datum', ['id' => $behandlung_art_id, 'datum' => $datum]);
        /* if ($resp['errors']) {
            return $resp;
        } */
        dbg('++++ Get rooms and current bookings from Hasura', $resp);

        // load necessary data in arrays for the work
        $raeume = $resp['data']['raeume'];

        foreach($raeume as $raum) {
            dbg('++++ Found a room', $raum['name']);

            $raum_geblockt = false;

            // loop thru blocker, if exists
            foreach($raum['blocker'] as $blocker) {
                if($blocker['wochentag'] == $wochentag) {
                  $raum_geblockt = true;
                  dbg('++++ Room is blocked');
                  break;
                }
            }

            if(!$raum_geblockt) {

                $start = strtotime($datum."T".$raum['uhrzeit_ab']);
                $ende = strtotime($datum."T".$raum['uhrzeit_bis']);

                dbg('++++ Starting at', $start);
                dbg('++++ Looping till', $ende);

                // how many bookings?
                $anz_buchungen = count($raum['behandlungen']);
                $idx_buchungen = 0; // start at first booking, if at least one exists

                while ($start < $ende) {

                    $next = $start + ($dauer * 60);

                    // are there any bookings?
                    if($anz_buchungen > $idx_buchungen) {

                        $start_buchung = strtotime($raum['behandlungen'][$idx_buchungen]['von']);
                        $ende_buchung = strtotime($raum['behandlungen'][$idx_buchungen]['bis']);

                        if($next <= $start_buchung) {
                            // ok.
                            dbg('++++ Push slot!');

                            $slots[$idx]['raum_id'] = $raum['id'];    
                            $slots[$idx]['raum'] = $raum['zentrum']['name']." ".$raum['name'];    
                            $slots[$idx]['datum'] = $datum;  
                            $slots[$idx]['start'] = $start;    
                            $slots[$idx]['ende'] = $next;  
                            //$slots[$idx]['start'] = date("Y-m-d H:i:s", $start);    
                            //$slots[$idx]['ende'] = date("Y-m-d H:i:s", $next);
                            $idx++;      

                            $start = $next;
                        } else {
                            // keep on trying
                            $start = $ende_buchung;
                            $idx_buchungen++;
                        }
                    } else {
                        // push anyway
                        dbg('++++ Push slot!');

                        $slots[$idx]['raum_id'] = $raum['id'];    
                        $slots[$idx]['raum'] = $raum['zentrum']['name']." ".$raum['name'];    
                        $slots[$idx]['datum'] = $datum;    
                        $slots[$idx]['start'] = $start;    
                        $slots[$idx]['ende'] = $next;
                        //$slots[$idx]['start'] = date("Y-m-d H:i:s", $start);    
                        //$slots[$idx]['ende'] = date("Y-m-d H:i:s", $next);
                        $idx++;  

                        $start = $next;
                    }

                    dbg('++++ Next stop at', $start);
                }

            }
        }

        return ['slots' => $slots];
    }


}