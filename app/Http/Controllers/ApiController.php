<?php

namespace App\Http\Controllers;

use DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ApiController extends BaseController
{
    /**
     * Get program
     * @param  string $day      Current day
     * @param  string $daytime  Current time of the day
     * @param  string $poule    Poule
     * @param  int $limit       Limit of results
     * @return json             Returns json array
     */
    public function getProgram($day = NULL, $daytime = NULL, $poule = NULL, $limit = NULL)
    {
        $dayanddaytime = $day . $daytime;
        if($day == 'zaterdag') {
            $program = DB::table('programma')
            ->where('poule', '=', $poule)
            ->where('dag', '=', $day)
            ->orderBy('starttijd', 'asc')
            ->limit($limit)
            ->get();
        } else {
            $program = DB::table('programma')
            ->where('poule', '=', $poule)
            ->where('dagdeel', '=', $dayanddaytime)
            ->where('dag', '=', $day)
            ->orderBy('starttijd', 'asc')
            ->limit($limit)
            ->get();
        }

    	return response()->json($program);
    }

    /**
     * Get Results
     * @param  string $day      Current day
     * @param  string $daytime  Current time of the day
     * @param  string $poule    Poule
     * @param  int $limit       Limit of results
     * @return json             Returns json array
     */
    public function getResults($day = NULL, $daytime = NULL, $poule = NULL, $limit = NULL)
    {
    	$dayanddaytime = $day . $daytime;
        if($day == 'zaterdag') {
            $program = DB::table('programma')
            ->where('poule', '=', $poule)
            ->where('dag', '=', $day)
            ->orderBy('starttijd', 'asc')
            ->limit($limit)
            ->get();
        } else {
            $program = DB::table('programma')
            ->where('poule', '=', $poule)
            ->where('dagdeel', '=', $dayanddaytime)
            ->where('dag', '=', $day)
            ->orderBy('starttijd', 'asc')
            ->limit($limit)
            ->get();
        }
        
        return response()->json($results);
    }

    /**
     * Get Poule Results
     * @param  string $day      Current day
     * @param  string $poule    Poule
     * @param  int $limit       Limit of results
     * @return json             Returns json array
     */
    public function getPouleResults($day = NULL, $poule = NULL, $limit = NULL)
    {
        $results = DB::table('stand')
            ->where('poule', '=', $poule)
            ->where('dag', '=', $day)
            ->orderBy('plaats', 'asc')
            ->limit($limit)
            ->get();

        return response()->json($results);
    }

    /**
     * Get last updated datetime
     * @return json             Returns json array
     */
    public function getLastUpdated()
    {
    	$lastupdate = DB::table('cron_updates')
            ->select('timestamp')
            ->where('programma', '=', 'Succesvol')
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->first();

        if(!empty($lastupdate))
        {
            $lastupdate = $lastupdate->timestamp;
        }

        return response()->json($lastupdate);
    }

    /**
     * Get data for pdf export
     * @param  string $day      Current day
     * @param  string $daytime  Current time of the day
     * @param  string $poule    Poule
     * @return json             Returns json array
     */
    public function getDataforExport($day = NULL, $dayTime = NULL, $poule = NULL)
    {
    	$export = $this->getProgram($day, $poule, $dayTime);

    	return response()->json($export);
    }

    /**
     * Get data for pdf export
     * @param  string $day      Current day
     * @param  string $daytime  Current time of the day
     * @param  string $poule    Poule
     * @return json             Returns json array
     */
    public function getDataforExportResults($day = NULL, $dayTime = NULL, $poule = NULL)
    {
    	$export = $this->getResults($day, $poule, $dayTime);

    	return response()->json($export);
    }

    /**
     * Get cron updates
     * @return json             Returns json array
     */
    public function cronUpdates()
    {
    	$cronupdates = DB::table('cron_updates')
                        ->orderBy('timestamp', 'desc')
			    		->get();
		return response()->json($cronupdates);
    }

    /**
     * Get all facebook accounts count
     * @return json             Returns json array
     */
    public function countFacebookAccounts()
    {
        $facebookaccounts = DB::table('oauth_identities')
                            ->select(DB::raw('count(*) as count'))
                            ->get();

        $facebookaccounts = $facebookaccounts[0]->count;

        return response()->json($facebookaccounts);
    }

    /**
     * Count all user accounts
     * @return json             Returns json array
     */
    public function countDefaultAccounts()
    {
        $users = User::count();
        return response()->json($users);
    }

    /**
     * Get all teams
     * @return json             Returns json array
     */
    public function getTeams()
    {
        $result = DB::table('programma')
                    ->select('team1', 'poule', 'dag')
                    ->distinct()
                    ->get();
        
        return response()->json($result);
    }

    /**
     * Insert last searched team
     */
    public function searchQueryTeam()
    {   
        $team = NULL;
        if(isset($_GET['team']) && $_GET['team'] != NULL) {
            $team = $_GET['team'];
            $result = DB::table('queries')
                       ->insert(['team' => $team, 'datum' => DB::raw('NOW()')]);
        }
    }

    /**
     * Insert results
     * @param  object       $request
     * @return json         Returns json array
     */
    public function insertResults(Request $request)
    {
        $postData = $request->all();
        if($postData != NULL) {
            $result = DB::table('programma')
                        ->where('id_programma', $postData['id'])
                        ->update(['uitslagen' => $postData['results']]);
            
            return response()->json(['status' => 200, 'response' => 'Updaten uitslagen gelukt']);
        }
    }

    public function getLatestResults()
    {
        $result = DB::table('programma')
                    ->orderBy('updated_at', 'asc')
                    ->get();

        return $response()->json($result);
    }
}
