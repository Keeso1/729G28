#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Programskelett för del B
# Erik Prytz [erik.prytz@liu.se], reviderad av Eva L. Ragnemalm

import pymysql
import re
import csv
import datetime

#If you like, define data-manipulation and cleaning functions here and use in the below functions.
#Other repeated code may also be turned into functions...

def fix_date(date):
  if date == None:
    return None
  if "-" in date:
    date = date.split("-")
    for num in date:
      if num.startswith("0"):
        num = num.replace('0', '')
    newdate = datetime.datetime(int(date[0]),int(date[1]),int(date[2]))

    return newdate.date()

  if "/" in date:
    newdate = []
    date = date.split("/")
    newdate.extend([int(date[2]),int(date[0]), int(date[1])])
    newdate = datetime.datetime(newdate[0],newdate[1],newdate[2])

    return newdate.date()

team_IDs = []
playerTeam_IDs = []

def insert_coaches():
  with open('AmericanFootballCoach.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      for key, value in row.items():
        row[key] = row[key].strip()
        if value == "NULL":
          row[key] = None

      for i in team_IDs:
        if i[1] == row["ID"]:
          team_ID = int(i[0])
        else:
          team_ID = None

      row["ID"] = int(row["ID"])
      row["birthDate"] = fix_date(row["birthDate"])
      
      insert_query= "INSERT INTO coach (ID, fullName, info, birthDate, birthPlace, team_ID) VALUES (%s, %s, %s, %s, %s, %s)"
      val = (row["ID"], row["name"], row["information"], row["birthDate"], row["birthPlace"], team_ID)
      try:
        cur.execute(insert_query, val) 
        conn.commit()
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
      
      if row["college"] != None and "{" in row["college"]:
        collegelist = row["college"].replace(row["college"][0], "")
        collegelist = collegelist.replace(row["college"][-1], "")
        collegelist = collegelist.split("|")
        collegequery = "INSERT INTO coachCollege (coach_ID, college) VALUES (%s, %s)"

        for college in collegelist:
          college = college.strip()
          collegeval = (row["ID"],college)
          cur.execute(collegequery, collegeval)

  csvfile.close()

def insert_teams():
  with open('AmericanFootballTeam.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      team_IDs.append((row["id"],row["coach"])) #teamIDs
      playerTeamIDs.append(row["id"], row["name"])
      for key, value in row.items():
        row[key] = row[key].strip()
        if value == "NULL":
          row[key] = None


      row["id"] = int(row["id"])
      year = row["start"].split("-")
      year = int(year[0])
      row["start"] = year
      
      
      insert_query= "INSERT INTO team (ID, fullName, ownerName, creationDate) VALUES (%s, %s, %s, %s)"
      val = (row["id"], row["name"], row["owner"], row["start"])
      try:
        cur.execute(insert_query, val)
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
      #Loop through the csv file, for each row: 
      #insert values into the table of teams and
      #also manage the data about which coach coaches the team.
      
      #Dates have one format but contains unneccesary detail (only the year is significant).
      #Strings may have unnecessary spaces in the beginning.
      #Some teams have no coach.
  csvfile.close()

def insert_players():
  with open('AmericanFootballPlayer.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      for key, value in row.items():
        row[key] = row[key].strip()
        if value == "NULL":
          row[key] = None
      print(row["alias"])

      row["ID"] = int(row["ID"])
      row["birthDate"] = fix_date(row["birthDate"])
      if row["draftYear"] == None:
        continue
      else:
        year = row["draftYear"].split("-")
        year = int(year[0])
        row["draftYear"] = year
      if row["weight"] == None:
        continue
      else:
        row["weight"] = float(row["weight"])
      if row["height"] == None:
        continue
      else:
        row["height"] = float(row["height"])

      insert_query= "INSERT INTO player (ID, fullName, draftYear, birthDate, birthPlace, _weight, height, info) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
      val = (row["ID"], row["name"], row["draftYear"], row["birthDate"], row["birthPlace"], row["weight"], row["height"], row["information"])
      try:
        cur.execute(insert_query, val)
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
      
      if row["alias"] != None and "{" in row["alias"]:
        aliaslist = row["alias"].replace(row["alias"][0], "")
        aliaslist = aliaslist.replace(row["alias"][-1], "")
        aliaslist = aliaslist.split("|")
        aliasquery = "INSERT INTO playerAlias (player_ID, alias) VALUES (%s, %s)"

        for alias in aliaslist:
          alias = alias.strip()
          aliasval = (row["ID"],alias)
          cur.execute(aliasquery, aliasval)

      if row["position"] != None and "{" in row["position"]:
        positionlist = row["position"].replace(row["position"][0], "")
        positionlist = positionlist.replace(row["position"][-1], "")
        positionlist = positionlist.split("|")
        positionquery = "INSERT INTO playerPosition (player_ID, position) VALUES (%s, %s)"

        for position in positionlist:
          position = position.strip()
          positionval = (row["ID"],position)
          cur.execute(positionquery, positionval)



      for i in playerTeam_IDs:
        if row["team"] != None and "{" in row["team"]:
          teamlist = row["team"].replace(row["team"][0], "")
          teamlist = teamlist.replace(row["team"][-1], "")
          teamlist = teamlist.split("|")
          for team in teamlist:
            if i[1] == row["team"]:
              playerTeam_ID = i[0]
              current = True
            else:
              current = False
            if i[1] == row["debutTeam"]:
              playerTeam_ID = i[0]
              debute = True
            else:
              debute = False
            if i[1] == row["formerTeam"]:
              playerTeam_ID = i[0]
              previous = True
            else:
              previous = False
        else:
          if i[1] == row["team"]:
            playerTeam_ID = i[0]
            current = True
          else:
            current = False
          if i[1] == row["debutTeam"]:
            playerTeam_ID = i[0]
            debute = True
          else:
            debute = False
          if i[1] == row["formerTeam"]:
            playerTeam_ID = i[0]
            previous = True
          else:
            previous = False #TODO FIX!
      
          
          
          


      insert_query= "INSERT INTO teamPlayer (team_ID, player_ID, debute, present, previous) VALUES (%s, %s, %s, %s, %s)"
      val = (playerTeam_ID, row["ID"], debute, current, previous)
      try:
        cur.execute(insert_query, val)
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
    #Loop through the csv file, for each row:
    #Insert information into the tables representing players, player aliases and player positions.
    #There may be several aliases and positions in a cell, or "NULL" which should be handled appropriately.
    #Dates have two different formats that must be converted.
    
    #Also (for each row) manage the information concerning the player´s debut team, current teams (the column Team) and former teams:
    #Debut teams are always single but may be NULL or team names that do not exist previously.
    #Team and Former Team may contain NULL, one team name or several team names that may or may not exist previously. "NULL" should be handled appropriately.
    #Team may also contain the string "Free Agent". This is not a team.
    
    #Team names that do not exist previously should be created (i.e. inserted into the table representing teams and given a new, unique, team ID).
    #Note that debut team, team and former team are given as names but should be replaced with team IDs in the appropriate tables.
  csvfile.close()


if __name__ == "__main__":
  #Create database connection, use the password that was sent to you by email
  try:
    conn = pymysql.connect(host='mariadb.edu.liu.se', port=3306, user='klabe908', passwd='klabe90879b6', db='klabe908')
    cur = conn.cursor()

    insert_teams()
    insert_coaches()
    insert_players()
    #print(team_IDs)

    #Close the database connection
    cur.close()
    conn.commit()
    conn.close()
  except BaseException as error:
    print("Något gick fel i databasen.", error)
