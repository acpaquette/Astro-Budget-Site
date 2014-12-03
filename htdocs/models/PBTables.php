<?php

require_once (dirname(__FILE__) . '/../tools/databaseClass.php');

class PBTables {

  var $dbh;

  function __construct() {
    $this->db = new DatabasePG ();
  }

  # People
  function addPerson ($name, $username) {
    $query = "INSERT INTO people (name, username) VALUES ('$name', '$username')";

    $this->db->query ($query);
  }

  function updatePerson ($peopleid, $name, $username) {
    if (!isset($peopleid)) { return "No people ID provided to update"; }

    if ((!isset($name)) and (!isset($username))) { return "A name or username update must be provided"; }

    $query = "UPDATE people SET ";
    if (isset($name)) {
      $query .= "name='$name'";
    }
    if ((isset($name)) and (isset($username))) {
      $query .= ", ";
    }
    if (isset($username)) {
      $query .= "username='$username'";
    }

    $query .= " WHERE peopleid=$peopleid";

    $this->db->query($query);
  }

  function getPerson ($peopleid, $name, $username) {
    $query = 'SELECT peopleid, name, username FROM people';

    $needAnd = false;
    if (isset($peopleid)) { 
      $query .= " WHERE peopleid=$peopleid"; 
      $needAnd = true;
    }
    if (isset($name)) {
      if ($needAnd) { $query .= " AND "; }
      else {
        $query .= " WHERE ";
        $needAnd = true;
      }
      $query .= "name='$name'";
    }
    if (isset($username)) {
      if ($needAnd) { $query .= " AND "; }
      else {
        $query .= " WHERE ";
        $needAnd = true;
      }
      $query .= "username='$username'";
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deletePerson ($peopleid) {
    $query = "DELETE FROM people WHERE peopleid=$peopleid";

    $this->db->query ($query);
  }

  # Salaries
  function addSalary ($peopleid, $effectivedate, $payplan, $title, $appttype, $authhours, $estsalary, $estbenefits,
                      $leavecategory, $laf) {
    if (!isset($peopleid)) { return "A people ID is needed to add salary information"; }

    $query = "INSERT INTO salaries (peopleid, effectivedate, payplan, title, appttype, authhours, " .
             "estsalary, estbenefits, leavecategory, laf) VALUES (" .
             "$peopleid, ";

    if (!isset($effectivedate)) { $query .= "now(), "; }
    else { $query .= "timestamp ('$effectivedate'), "; }

    $query .= "'$payplan', '$title', '$appttype', $authhours, $estsalary, $estbenefits, $leavecategory, $laf";

    $this->db->query($query);
  }

  function updateSalary ($salaryid, $peopleid, $effectivedate, $payplan, $title, $appttype, $authhours, 
                         $estsalary, $estbenefits, $leavecategory, $laf) {
    if (!isset($salaryid)) { return "A salary ID must be provided to update"; }

    if (!(isset($peopleid) or isset($effectivedate) or isset($payplan) or isset($title) or isset($appttype)
          or isset($authhours) or isset($estsalary) or isset($estbenefits) or isset($leavecategory) or isset($laf))) {
      return "At least one value must be provided to update salary information";
    }

    $needComma = false;
    $query = "UPDATE salaries SET ";
    if (isset($peopleid)) {
      $query .= "peopleid=$peopleid";
      $needComma = true;
    }
    if (isset($effectivedate)) {
      if ($needComma) { $query .= ", "; }
      $query .= "effectivedate=timestamp('$effectivedate')";
      $needComma = true;
    }
    if (isset($payplan)) {
      if ($needComma) { $query .= ", "; }
      $query .= "payplan='$payplan'";
      $needComma = true;
    }
    if (isset($title)) {
      if ($needComma) { $query .= ", "; }
      $query .= "title='$title'";
      $needComma = true;
    }
    if (isset($appttype)) {
      if ($needComma) { $query .= ", "; }
      $query .= "appttype='$appttype'";
      $needComma = true;
    }
    if (isset($authhours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "authhours=$authhours";
      $needComma = true;
    }
    if (isset($estsalary)) {
      if ($needComma) { $query .= ", "; }
      $query .= "estsalary=$estsalary";
      $needComma = true;
    }
    if (isset($estbenefits)) {
      if ($needComma) { $query .= ", "; }
      $query .= "estbenefits=$estbenefits";
      $needComma = true;
    }
    if (isset($leavecategory)) {
      if ($needComma) { $query .= ", "; }
      $query .= "leavecategory=$leavecategory";
      $needComma = true;
    }
    if (isset($laf)) {
      if ($needComma) { $query .= ", "; }
      $query .= "laf=$laf";
    }

    $query .= " WHERE salaryid=$salaryid";

    $this->db->query($query);
  }

  function getEffectiveSalary ($peopleid, $targetdate) {
    if (!isset($peopleid)) { return "No ID provided to lookup effective salary for"; }

    $query = "SELECT salaryid, peopleid, effectivedate, payplan, title, appttype, " .
             "authhours, estsalary, estbenefits, leavecategory, laf FROM salaries " .
             "WHERE peopleid=$peopleid";

    if (isset($targetdate)) {
      $query .= " AND effectivedate < timestamp ('$targetdate')";
    }
    else {
      $query .= " AND effectivedate < now()";
    }

    $query .= " ORDER BY effectivedate DESC LIMIT 1";

    $this->db->query($query);
    $results = $this->db->getResultRow();

    return ($results);
  }

  function getSalary ($peopleid) {
    if (!isset($peopleid)) { return "No ID provided to lookup salary information for"; }

    $query = "SELECT salaryid, peopleid, effectivedate, payplan, title, appttype, " .
             "authhours, estsalary, estbenefits, leavecategory, laf FROM salaries " .
             "WHERE peopleid=$peopleid";

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteSalary ($salaryid) {
    if (!isset($salaryid)) { return "A salary ID must be provided to delete"; }

    $query = "DELETE FROM salaries WHERE salaryid=$salaryid";

    $this->db->query($query);
  }

#CREATE TABLE salaries (
#  salaryid SERIAL Primary Key,
#  peopleid INTEGER,
#  effectivedate TIMESTAMP,
#  payplan VARCHAR(32),
#  title VARCHAR(128),
#  appttype VARCHAR(8),
#  authhours REAL,
#  estsalary REAL,
#  estbenefits REAL,
#  leavecategory REAL,
#  laf REAL,

  # FundingPrograms
  function addFundingProgram ($programname, $agency, $pocname, $pocemail, $startdate, $enddate) {
    if (!isset($programname)) { return "The program name must be set to add a funding program"; }
    if (!isset($agency)) { return "The agency must be set to add a funding program"; }

    $query = "INSERT INTO fundingprograms (programname, agency, pocname, pocemail, startdate, enddate) " .
             "VALUES ('$programname', '$agency', '$pocname', '$pocemail', timestamp('$startdate'), " .
             "timestamp ('$enddate')";

    $this->db->query($query);
  }

  function updateFundingProgram ($programid, $programname, $agency, $pocname, $pocemail, $startdate, $enddate) {
    if (!isset($programid)) { return "Must provide a program ID to update information"; }

    if (!(isset($programname) or isset($agency) or isset($pocname) or isset($pocemail) or 
          isset($startdate) or isset($enddate))) {
      return "At least one field must be provided to update funding program";
    }

    $needComma = false;
    $query = "UPDATE fundingprograms SET ";
    if (isset($programname)) {
      $query .= "programname='$programname'";
      $needComma = true;
    }
    if (isset($agency)) {
      if ($needComa) { $query .= ", "; }
      $query .= "agency='$agency'";
      $needComma = true;
    }
    if (isset($pocname)) {
      if ($needComa) { $query .= ", "; }
      $query .= "pocname='$pocname'";
      $needComma = true;
    }
    if (isset($pocemail)) {
      if ($needComa) { $query .= ", "; }
      $query .= "pocemail='$pocemail'";
      $needComma = true;
    }
    if (isset($startdate)) {
      if ($needComa) { $query .= ", "; }
      $query .= "startdate= timestamp ('$startdate')";
      $needComma = true;
    }
    if (isset($enddate)) {
      if ($needComa) { $query .= ", "; }
      $query .= "enddate=timestamp('$enddate')";
      $needComma = true;
    }

    $query .= " WHERE programid=$programid";

    $this->db->query($query);
  }

  function getFundingPrograms ($programid, $programname, $agency, $pocname, $pocemail, $targetdate) {
    $query = "SELECT programid, programname, agency, pocname, pocemail, startdate, enddate " .
             "FROM fundingprograms";

    $needAnd = false;
    if (isset($programid)) {
      $query .= "WHERE programid=$programid";
      $needAnd = true;
    }
    if (isset($programname)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "programname='$programname'";
    }
    if (isset($agency)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "agency='$agency'";
    }
    if (isset($pocname)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "pocname='$pocname'";
    }
    if (isset($pocemail)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "pocemail='$pocemail'";
    }
    if (isset($targetdate)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "startdate > timestamp('$targetdate') AND enddate < timestamp('$targetdate')";
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteFundingProgram ($programid) {
    if (!isset($programid)) { return "No funding program ID specified to delete"; }

    $query = "DELETE FROM fundingprograms WHERE programid=$programid";

    $this->db->query($query);
  }

#CREATE TABLE fundingprograms (
#  programid SERIAL Primary Key,
#  programname VARCHAR(256),
#  agency VARCHAR(32),
#  pocname VARCHAR(128),
#  pocemail VARCHAR(128),
#  startdate TIMESTAMP,
#  enddate TIMESTAMP

  # Proposals
  function addProposal ($peopleid, $projectname, $proposalnumber, $awardnumber, $programid,
                        $perfperiodstart, $perfperiodend) {
    if (!(isset($peopleid) and isset($projectname))) { 
      return "A PI and project name must be provided to create a proposal"; 
    }

    $query = "INSERT INTO proposals (peopleid, projectname, proposalnumber, awardnumber, " .
             "programid, perfperiodstart, perfperiodend) VALUES ($peopleid, '$projectname', " .
             "'$proposalnumber', '$awardnumber', $programid, ";
    if (isset($perfperiodstart)) { $query .= "timestamp('$perfperiodstart'), "; }
    else { $query .= "null, "; }
    if (isset($perfperiodend)) { $query .= "timestamp('$perfperiodend'), "; }
    else { $query .= "null, "; }

    $this->db->query($query);
  }

  function updateProposal ($proposalid, $peopleid, $projectname, $proposalnumber, $awardnumber, $programid,
                        $perfperiodstart, $perfperiodend) {
    if (!isset($proposalid)) { return "A proposal ID is required for an update"; }
    if (!(isset($peopleid) or isset($projectname) or isset($proposalnumber) or isset($awardnumber) or
          isset($programid) or isset($perfperiodstart) or isset($perfperiodend))) {
      return "No changed provided to update proposals with";
    }

    $query = "UPDATE proposals SET ";
    $needComma = false;

    if (isset($peopleid)) {
      $query .= "peopleid=$peopleid";
      $needComma = true;
    }
    if (isset($projectname)) {
      if ($needComma) { $query .= ", "; }
      $query .= "projectname='$projectname'";
      $needComma = true;
    }
    if (isset($proposalnumber)) {
      if ($needComma) { $query .= ", "; }
      $query .= "proposalnumber='$proposalnumber'";
      $needComma = true;
    }
    if (isset($awardnumber)) {
      if ($needComma) { $query .= ", "; }
      $query .= "awardnumber='$awardnumber'";
      $needComma = true;
    }
    if (isset($programid)) {
      if ($needComma) { $query .= ", "; }
      $query .= "programid=$programid";
      $needComma = true;
    }
    if (isset($perfperiodstart)) {
      if ($needComma) { $query .= ", "; }
      $query .= "perfperiodstart=timestamp('$perfperiodstart')";
      $needComma = true;
    }
    if (isset($perfperiodend)) {
      if ($needComma) { $query .= ", "; }
      $query .= "perfperiodend=timestamp('$perfperiodend')";
      $needComma = true;
    }

    $query .= " WHERE proposalid=$proposalid";

    $this->db->query($query);
  }

  function getProposals ($proposalid, $peopleid, $programid, $awardnumber, $proposalnumber, $perfperiod) {
    $query = "SELECT proposalid, peopleid, programid, awardnumber, proposalnumber, perfperiodstart, perfperiodend " .
             "FROM proposals";
    $needAnd = false;

    if (isset($proposalid)) {
      $query .= " WHERE proposalid=$proposalid";
      $needAnd = true;
    }
    if (isset($peopleid)) {
      if ($needAnd) { $query .= " AND ";}
      else { $query .= " WHERE "; }
      $query .= "peopleid=$peopleid";
      $needAnd = true;
    }
    if (isset($programid)) {
      if ($needAnd) { $query .= " AND ";}
      else { $query .= " WHERE "; }
      $query .= "programid=$programid";
      $needAnd = true;
    }
    if (isset($awardnumber)) {
      if ($needAnd) { $query .= " AND ";}
      else { $query .= " WHERE "; }
      $query .= "awardnumber='$awardnumber'";
      $needAnd = true;
    }
    if (isset($proposalnumber)) {
      if ($needAnd) { $query .= " AND ";}
      else { $query .= " WHERE "; }
      $query .= "proposalnumber='$proposalnumber'";
      $needAnd = true;
    }
    if (isset($perfperiod)) {
      if ($needAnd) { $query .= " AND ";}
      else { $query .= " WHERE "; }
      $query .= "perfperiodstart < timestamp('$perfperiod') AND perfperiodend > timestamp('$perfperiod')";
      $needAnd = true;
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteProposal ($proposalid) {
    if (!isset($proposalid)) { return "A proposal ID is required to delete a proposal"; }
    $query = "DELETE FROM proposals WHERE proposalid=$proposalid";
    
    $this->db->query($query);
  }

  # CREATE TABLE proposals (
  #   proposalid SERIAL Primary Key,
  #   peopleid INTEGER,
  #   projectname VARCHAR(256),
  #   proposalnumber VARCHAR(128),
  #   awardnumber VARCHAR(128),
  #   programid INTEGER,
  #   perfperiodstart TIMESTAMP,
  #   perfperiodend TIMESTAMP,

  # FBMSAccounts
  function addFBMSAccount ($accountno, $proposalid) {
    if (!(isset($accountno) and isset($proposalid))) { return "Both the account No and proposal ID are required"; }

    $query = "INSERT INTO fbmsaccounts (accountno, proposalid) VALUES ('$accountno', $proposalid)";

    $this->db->query($query);
  }
  
  function updateFBMSAccount ($fbmsid, $accountno, $proposalid) {
    if (!isset($fbmsid)) { return "No FBMS account ID specified to update"; }

    if (!(isset($accountno) or isset($proposalid))) { 
      return "The Account No or proposal ID are required to update FBMS"; 
    }

    $query = "UPDATE fbmsaccounts SET";
    if (isset($accountno)) { $query .= "accountno=$accountno"; }
    if (isset($proposalid)) {
      if (isset($accountno)) { $query .= ", "; }
      $query .= "proposalid=$proposalid";
    }
    $query .= " WHERE fbmsid=$fbmsid";

    $this->db->query($query);
  }  
  
  function getFBMSAccounts ($fbmsid, $accountno, $proposalid) {
    $query = "SELECT fmbsid, accountno, proposalid FROM fbmsaccounts";
    $needAnd = false;
    if (isset($fbmsid)) {
      $query .= " WHERE fbmsid=$fbmsid";
      $needAnd = true;
    }
    if (isset($accountno)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "accountno='$accountno'";
    }
    if (isset($proposalid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "proposalid='$proposalid'";
    }
    
    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteFBMSAccount ($fbmsid) {
    if (!isset($fbmsid)) { return "A FBMS ID must be provided to delete an entry"; }

    $query = "DELETE FROM fbmsaccounts WHERE fbmsid=$fbmsid";

    $this->db->query($query);
  }

  # CREATE TABLE fbmsaccounts (
  #  fbmsid SERIAL Primary Key,
  #  accountno VARCHAR(128),
  #  proposalid INTEGER,
  
  # Conferences
  function addConference ($meeting, $location) {
    if (!(isset($meeting) or isset($location))) { return "A meeting name or location must be provided"; }

    $query = "INSERT INTO conferences (meeting, location) VALUES ('$meeting', '$location')";

    $this->db->query($query);
  }

  function updateConference ($conferenceid, $meeting, $location) {
    if (!isset($conferenceid)) { return "A conference ID must be provided to update conferences"; }
    if (!(isset($meeting) or isset($location))) { return "A meeting name or location must be provided"; }

    $query = "UPDATE conferences SET ";
    if (isset($meeting)) { $query .= "meeting='$meeting'"; }
    if (isset($location)) {
      if (isset($meeting)) { $query .= ", "; }
      $query .= "location='$location'";
    }
    $query .= " WHERE conferenceid=$conferenceid";

    $this->db->query($query);
  }

  function getConferences ($conferenceid, $meeting, $location) {
    $query = "SELECT conferenceid, meeting, location FROM conferences";
    $needAnd = false;
    if (isset($conferenceid)) {
      $query .= " WHERE conferenceid=$conferenceid";
      $needAnd = true;
    }
    if (isset($meeting)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "meeting='$meeting'";
    }
    if (isset($location)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $needAnd = true;
      $query .= "location='$location'";
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteConference ($conferenceid) {
    if (!isset($conferenceid)) { return "A conference ID must be provided to delete"; }

    $query = "DELETE FROM conferences WHERE conferenceid=$conferenceid";

    $this->db->query($query);
  }

  # CREATE TABLE conferences (
  #  conferenceid SERIAL Primary Key,
  #  meeting VARCHAR(256),
  #  location VARCHAR(128)
  
  # ConferenceRates
  function addConferenceRate ($conferenceid, $effectivedate, $perdiem, $registration, $groundtransport, $airfare) {
    if (!isset($conferenceid)) { return "A conference ID must be provided to add new conference rates"; }

    $query = "INSERT INTO conferencerates (conferenceid, effectivedate, perdiem, registration, " .
             "groundtransport, airfare) VALUES ($conferenceid, ";
    if (isset($effectivedate)) { $query .= "timestamp ('$effectivedate'), "; }
    else { $query .= "now(), "; }
    $query .= "$perdiem, $registration, $groundtransport, $airfare)";

    $this->db->query($query);
  }

  function updateConferenceRate ($conferencerateid, $conferenceid, $effectivedate, $perdiem, 
                                $registration, $groundtransport, $airfare) {
    if (!isset($conferencerateid)) { return "A conference rate ID must be provided for an update"; }
    if (!(isset($effectivedate) or isset($perdiem) or isset($registration) 
       or isset($groundtransport) or isset ($airfare))) { return "Nothing to change in conference rate update"; }

    $query = "UPDATE conferencerates SET ";
    $needComma = false;
    if (isset($conferenceid)) {
      $query .= "conferenceid=$conferenceid";
      $needComma = true;
    }
    if (isset($effectivedate)) {
      if ($needComma) { $query .= ", "; }
      $query .= "effectivedate = timestamp('$effectivedate')";
      $needComma = true;
    }
    if (isset($perdiem)) {
      if ($needComma) { $query .= ", "; }
      $query .= "perdiem=$perdiem";
      $needComma = true;
    }
    if (isset($registration)) {
      if ($needComma) { $query .= ", "; }
      $query .= "registration=$registration";
      $needComma = true;
    }
    if (isset($groundtransport)) {
      if ($needComma) { $query .= ", "; }
      $query .= "groundtransport=$groundtransport";
      $needComma = true;
    }
    if (isset($airfare)) {
      if ($needComma) { $query .= ", "; }
      $query .= "airfare=$airfare";
      $needComma = true;
    }

    $query .= " WHERE conferencerateid=$conferencerateid";

    $this->db->query($query);
  }

  function getConferenceRates ($conferenceid, $effectivedate) {
    if (!isset($conferenceid)) { return "A conference ID must be provided to list conference rates"; }

    $query = "SELECT conferencerateid, conferenceid, effectivedate, perdiem, registration, " .
             "groundrate, airfare FROM conferencerates WHERE conferenceid=$conferenceid";
    if (isset($effectivedate)) { 
      $query .= " AND effectivedate > timestamp('$effectivedate')"; 
      $query .= " ORDER BY effectivedate DESC LIMIT 1";
    }
    else { $query .= " ORDER BY effectivedate DESC"; }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteConferenceRate ($conferencerateid) {
    if (!isset($conferenceid)) { return "A conference rate ID must be provided to delete"; }

    $query = "DELETE FROM conferencerates WHERE conferencerateid=$conferencerateid";

    $this->db->query($query);
  }

  # CREATE TABLE conferencerates (
  #  conferencerateid SERIAL Primary Key,
  #  conferenceid INTEGER,
  #  effectivedate TIMESTAMP,
  #  perdiem REAL,
  #  registration REAL,
  #  groundtransport REAL,
  #  airfare REAL,
  
  # ConferenceAttendee
  function addConferenceAttendee ($conferenceid, $proposalid, $peopleid, $meetingdays, $traveldays, $startdate) {
    if (!(isset($conferenceid) and isset($proposalid) and isset($peopleid))) {
      return "Missing required information to add conference attendee";
    }

    $query = "INSERT INTO conferenceattendee (conferenceid, proposalid, peopleid, meetingdays, traveldays, statedate)".
             " VALUES ($conferenceid, $proposalid, $peopleid, $meetingdays, $traveldays, ";
    if (isset($startdate)) { $query .= "timestamp ('$startdate'))"; }
    else { $query .= "now())"; }

    $this->db->query($query);
  }

  function updateConferenceAttendee ($conferenceattendeeid, $conferenceid, $proposalid, $peopleid, $meetingdays, 
                                    $traveldays, $startdate) {
    if (!isset($conferenceattendeeid)) { return "A conference attendee ID must be provided for an update"; }
    if (!(isset($conferenceid) or isset($proposalid) or isset($peopleid) or isset($meetingdays)
       or isset($traveldays) or isset($startdate))) { return "No changes provided for conference attendee update"; }

    $needComma = false;
    if (isset($conferenceid)) {
      $query .= "conferenceid=$conferenceid";
      $needComma = true;
    }
    if (isset($proposalid)) {
      if ($needComma) { $query .= ", "; }
      $needComma = true;
      $query .= "proposalid=$proposalid";
    }
    if (isset($peopleid)) {
      if ($needComma) { $query .= ", "; }
      $needComma = true;
      $query .= "peopleid=$peopleid";
    }
    if (isset($meetingdays)) {
      if ($needComma) { $query .= ", "; }
      $needComma = true;
      $query .= "meetingdays=$meetingdays";
    }
    if (isset($traveldays)) {
      if ($needComma) { $query .= ", "; }
      $needComma = true;
      $query .= "traveldays=$traveldays";
    }
    if (isset($startdate)) {
      if ($needComma) { $query .= ", "; }
      $needComma = true;
      $query .= "startdate=timestamp('$startdate')";
    }

    $this->db->query($query);
  }
    
  function getConferenceAttendees ($confereneceattendeeid, $conferenceid, $proposalid, $peopleid) {
    $query = "SELECT conferenceattendeeid, conferenceid, proposalid, peopleid, meetingdays, traveldays, startdate " .
             "FROM conferenceattendees";

    $needAnd = false;
    if (isset($conferenceattendeeid)) {
      $query .= " WHERE conferenceattendeeid=$conferenceattendeeid";
      $needAnd = true;
    }
    if (isset($conferenceid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "conferenceid=$conferenceid";
      $needAnd = true;
    }
    if (isset($proposalid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "proposalid=$proposalid";
      $needAnd = true;
    }
    if (isset($peopleid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "peopleid=$peopleid";
      $needAnd = true;
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteConferenceAttendee ($conferenceattendeeid) {
    if (!isset($conferenceattendeeid)) { return "A conference attendee ID must be provided to delete"; }

    $query = "DELETE FROM conferenceattendee WHERE conferenceattendeeid=$conferenceattendeeid";

    $this->db->query($query);
  }
  
  # CREATE TABLE conferenceattendee (
  #  conferenceattendeeid SERIAL Primary Key,
  #  conferenceid INTEGER,
  #  proposalid INTEGER,
  #  peopleid INTEGER,
  #  meetingdays INTEGER,
  #  traveldays INTEGER,
  #  startdate TIMESTAMP,
  
  # Tasks
  function addTask ($proposalid, $taskname) {
    if (!(isset($proposalid) and isset($taskname))) { return "Both a proposal ID and a task name are required"; }

    $query = "INSERT INTO tasks (proposalid, taskname) VALUES ($proposalid, '$taskname')";

    $this->db->query($query);
  }
  
  function updateTask ($taskid, $proposalid, $taskname) {
    if (!isset($taskid)) { return "A task ID is required to update tasks"; }
    if (!(isset($proposalid) or isset($taskname))) { return "No changes provided to update tasks"; }

    $query = "UPDATE tasks SET ";
    if (isset($proposalid)) { $query .= "proposalid=$proposalid"; }
    if (isset($taskname)) {
      if (isset($proposalid)) { $query .= ", "; }
      $query .= "taskname='$taskname'";
    }

    $this->db->query($query);
  }
  
  function getTasks ($taskid, $proposalid, $taskname) {
    $query = "SELECT taskid, proposalid, taskname FROM tasks";

    $needAnd = false;
    if (isset($taskid)) {
      $query .= " WHERE taskid=$taskid";
      $needAnd = true;
    }
    if (isset($proposalid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "proposalid=$proposalid";
      $needAnd = true;
    }
    if (isset($taskname)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "taskname=$taskname";
      $needAnd = true;
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteTask ($taskid) {
    if (!isset($taskid)) { return "A task ID is required to delete a task"; }

    $query = "DELETE FROM tasks WHERE taskid=$taskid";
    
    $this->db->query($query);
  }

  # CREATE TABLE tasks (
  #  taskid BIGSERIAL Primary Key,
  #  proposalid INTEGER,
  #  taskname VARCHAR(1024),
  
  # Staffing
  function addStaffing ($taskid, $peopleid, $fiscalyear, $q1hours, $q2hours, $q3hours, $q4hours, $flexhours) {
    if (!(isset($taskid) and isset($peopleid) and isset($fiscalyear))) { 
      return "A task, person, and FY are required for staffing"; 
    }

    $query = "INSERT INTO staffing (taskid, peopleid, fiscalyear, q1hours, q2hours, q3hours, q4hours, flexhours) " .
             "VALUES ($taskid, $peopleid, '$fiscalyear', $q1hours, $q2hours, $q3hours, $q4hours, $flexhours)";

    $this->db->query($query);
  }

  function updateStaffing ($staffingid, $taskid, $peopleid, $fiscalyear, 
                          $q1hours, $q2hours, $q3hours, $q4hours, $flexhours) {
    if (!isset($staffingid)) { return "A staffing ID is required to make an update"; }
    if (!(isset($taskid) or isset($peopleid) or isset($fiscalyear) or isset($q1hours) or isset($q2hours) or
          isset($q3hours) or isset($q4hours) or isset($flexhours))) { "No change provided to update staffing"; }

    $query = "UPDATE STAFFING SET ";
    $needComma = false;

    if (isset($taskid)) {
      $query .= "taskid=$taskid";
      $needComma = true;
    }
    if (isset($peopleid)) {
      if ($needComma) { $query .= ", "; }
      $query .= "peopleid=$peopleid";
      $needComma = true;
    }
    if (isset($fiscalyear)) {
      if ($needComma) { $query .= ", "; }
      $query .= "fiscalyear='$fiscalyear'";
      $needComma = true;
    }
    if (isset($q1hours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "q1hours=$q1hours";
      $needComma = true;
    }
    if (isset($q2hours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "q2hours=$q2hours";
      $needComma = true;
    }
    if (isset($q3hours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "q3hours=$q3hours";
      $needComma = true;
    }
    if (isset($q4hours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "q4hours=$q4hours";
      $needComma = true;
    }
    if (isset($flexhours)) {
      if ($needComma) { $query .= ", "; }
      $query .= "flexhours=$flexhours";
      $needComma = true;
    }

    $this->db->query($query);
  }

  function getStaffing ($staffingid, $taskid, $peopleid, $fiscalyear) {
    $query = "SELECT staffingid, taskid, peopleid, fiscalyear, q1hours, q2hours, q3hours, q4hours, flexhours " .
             "FROM staffing";
    $needAnd = false;

    if (isset($staffingid)) {
      $query .= " WHERE staffingid=$staffingid";
      $needAnd = true;
    }
    if (isset($taskid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "taskid=$taskid";
      $needAnd = true;
    }
    if (isset($peopleid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "peopleid=$peopleid";
      $needAnd = true;
    }
    if (isset($fiscalyear)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "fiscalyear='$fiscalyear'";
      $needAnd = true;
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteStaffing ($staffingid) {
    if (!isset($staffingid)) { return "A staffing ID is required for a delete"; }
    $query = "DELETE FROM staffing WHERE staffingid=$staffingid";

    $this->db->query($query);
  }

  # CREATE TABLE staffing (
  #  staffingid BIGSERIAL Primary Key,
  #  taskid BIGINT,
  #  peopleid INTEGER,
  #  fiscalyear VARCHAR(4),
  #  q1hours REAL,
  #  q2hours REAL,
  #  q3hours REAL,
  #  q4hours REAL,
  #  flexhours REAL,
  
  # ExpenseTypes
  function addExpenseType ($description) {
    if (!isset($description)) { return "No description provided to add expense type"; }

    $query = "INSERT INTO expensetypes (description) VALUES ('$description')";

    $this->db->query($query);
  }

  function updateExpenseType ($expensetypeid, $description) {
    if (!(isset($expensetypeid) and isset($description))) { return "No ID or change provided to update expense types"; }
    
    $query = "UPDATE expensetypes SET description='$description' WHERE expensetypeid=$expensetypeid";
    
    $this->db->query($query);
  }
  
  function getExpenseTypes ($expensetypeid, $description) {
    $query = "SELECT expensetypeid, description) FROM expensetypes";

    if (isset($expensetypeid)) { $query .= " WHERE expensetypeid=$expensetypeid"; }

    if (isset($description)) {
      if (isset($expensetypeid)) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "description='$description'";
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }
  
  function deleteExpenseType ($expensetypeid) {
    if (!isset($expensetypeid)) { return "No expense type ID provided to delete"; }

    $query = "DELETE FROM expensetypes WHERE expensetypeid=$expensetypeid";

    $this->db->query($query);
  }

  # CREATE TABLE expensetypes (
  #  expensetypeid SERIAL Primary Key,
  #  description VARCHAR(256)

  # Expenses
  function addExpense ($proposalid, $expensetypeid, $description, $amount, $fiscalyear) {
    if (!(isset($proposalid) and isset($expensetypeid) and isset($amount))) {
      return "Missing required fields to add a new expense";
    }

    $query = "INSERT INTO expenses (proposalid, expensetypeid, description, amount, fiscalyear) VALUES " .
             "($proposalid, $expensetypeid, '$description', $amount, '$fiscalyear')";

    $this->db->query($query);
  }

  function updateExpense ($expenseid, $proposalid, $expensetypeid, $description, $amount, $fiscalyear) {
    if (!isset($expenseid)) { return "An expense ID is required to update expenses"; }
    if (!(isset($proposalid) or isset($expensetypeid) or isset($description) or isset($amount) or isset($fiscalyear))) {
      return "No changes provided to update expenses with";
    }

    $query = "UPDATE expenses SET ";
    $needComma = false;
    if (isset($proposalid)) {
      $query .= "proposalid=$proposalid";
      $needComma = true;
    }
    if (isset($expensetypeid)) {
      if ($needComma) { $query .= ", "; }
      $query .= "expensetypeid=$expensetypeid";
      $needComma = true;
    }
    if (isset($description)) {
      if ($needComma) { $query .= ", "; }
      $query .= "description='$description'";
      $needComma = true;
    }
    if (isset($amount)) {
      if ($needComma) { $query .= ", "; }
      $query .= "amount=$amount";
      $needComma = true;
    }
    if (isset($fiscalyear)) {
      if ($needComma) { $query .= ", "; }
      $query .= "fiscalyear='$fiscalyear'";
      $needComma = true;
    }

    $query .= " WHERE expenseid=$expenseid";
    
    $this->db->query($query);
  }

  function getExpenses ($expenseid, $proposalid, $expensetypeid, $fiscalyear) {
    $query = "SELECT expenseid, proposalid, expensetypeid, description, amount, fiscalyear FROM expenses";

    $needAnd = false;
    if (isset($expenseid)) {
      $query .= " WHERE expenseid=$expenseid";
      $needAnd = true;
    }
    if (isset($proposalid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "proposalid=$proposalid";
      $needAnd = true;
    }
    if (isset($expensetypeid)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "expensetypeid=$expensetypeid";
      $needAnd = true;
    }
    if (isset($fiscalyear)) {
      if ($needAnd) { $query .= " AND "; }
      else { $query .= " WHERE "; }
      $query .= "fiscalyear='$fiscalyear'";
      $needAnd = true;
    }

    $this->db->query($query);
    $results = $this->db->getResultArray();

    return ($results);
  }

  function deleteExpense ($expenseid) {
    if (!isset($expenseid)) { return "No expense ID provided to delete"; }

    $query = "DELETE FROM expenses WHERE expenseid=$expenseid";

    $this->db->query($query);
  }

  # CREATE TABLE expenses (
  #  expenseid BIGSERIAL Primary Key,
  #  proposalid INTEGER,
  #  expensetypeid INTEGER,
  #  description VARCHAR(256),
  #  amount REAL,
  #  fiscalyear VARCHAR(4),

  function addAdministrator ($peopleid) {
    if (!isset($peopleid)) { return "A people ID must be provided to add an administrator"; }

    $query = "INSERT INTO administrators (peopleid) VALUES ($peopleid)";

    $this->db->query($query);
  }

  function isAdmin ($peopleid) {
    if (!isset($peopleid)) { return "A people ID must be provided to look up if they are an administrator"; }

    $query = "SELECT adminid, peopleid FROM administrators WHERE peopleid=$peopleid";

    $this->db->query($query);
    $results = $this->db->getResultArray();

    if (count($results) > 0) { return true; }

    return false;
  }

  function deleteAdmin ($adminid) {
    if (!isset($adminid)) { return "An administrator ID is required to delete an administrator"; }

    $query = "DELETE FROM administrators WHERE adminid=$adminid";

    $this->db->query($query);
  }

  # CREATE TABLE administrators (
  #  adminid SERIAL Primary Key,
  #  peopleid INTEGER,

}
  
?>
