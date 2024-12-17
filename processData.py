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

def fix_datatype(input):
  if input == None:
    return
  if "-" in input:
    output = input.split("-")
    output = int(output[0])
    return output
  else:
    return float(input)
  
def insert_multivalued(id, dictitem, tablename,value_tuple):
  if dictitem != None and "{" in dictitem:
        itemlist = dictitem.replace(dictitem[0], "")
        itemlist = itemlist.replace(dictitem[-1], "")
        itemlist = itemlist.split("|")

        get_valuelen = value_tuple.split(",")
        placeholders = ', '.join(['%s'] * len(get_valuelen))
        itemquery = f"INSERT INTO {tablename} {value_tuple} VALUES ({placeholders})"

        for item in itemlist:
          item = item.strip()
          itemval = (id,item)
          cur.execute(itemquery, itemval)
          
def checkIfNoneAndStrip(row):
  for key, value in row.items():
    row[key] = row[key].strip()
    if value == "NULL":
      row[key] = None


team_IDs = []
teamName_and_IDs = []

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
      
      #Insert into coachcollege table
      insert_multivalued(row["ID"], row["college"], "coachCollege","(coach_ID, college)")

  csvfile.close()

def insert_teams():
  with open('AmericanFootballTeam.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      checkIfNoneAndStrip(row)
      team_IDs.append((row["id"],row["coach"])) #teamIDs
      teamName_and_IDs.append((row["id"], row["name"]))


      row["id"] = int(row["id"])
      row["start"] = fix_datatype(row["start"])
      
      
      insert_query= "INSERT INTO team (ID, fullName, ownerName, creationDate) VALUES (%s, %s, %s, %s)"
      val = (row["id"], row["name"], row["owner"], row["start"])
      try:
        cur.execute(insert_query, val)
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
  csvfile.close()
      

def insert_players():
  with open('AmericanFootballPlayer.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      checkIfNoneAndStrip(row)

      row["ID"] = int(row["ID"])
      row["birthDate"] = fix_date(row["birthDate"])
      row["draftYear"] = fix_datatype(row["draftYear"])
      row["weight"] = fix_datatype(row["weight"])
      row["height"] = fix_datatype(row["height"])
      

      insert_query= "INSERT INTO player (ID, fullName, draftYear, birthDate, birthPlace, _weight, height, info) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
      val = (row["ID"], row["name"], row["draftYear"], row["birthDate"], row["birthPlace"], row["weight"], row["height"], row["information"])
      
      try:
        cur.execute(insert_query, val)
      except BaseException as error:
        print("något gick fel i databasen vid insättning av coach: ", error)
      
      #Insert into playerAlias and playerPosition table
      insert_multivalued(row["ID"],row["alias"], "playerAlias","(player_ID, alias)")
      insert_multivalued(row["ID"],row["position"], "playerPosition","(player_ID, position)")
  csvfile.close()

def insert_teamPlayers():
  with open('AmericanFootballPlayer.csv', 'r') as csvPlayers:
    players = csv.DictReader(csvPlayers, delimiter = ',')

    for player_row in players:
      checkIfNoneAndStrip(player_row)
      player_id = int(player_row["ID"])

      current_teams = player_row["team"].split(',')  # Teams the player is currently in (present)
      former_teams = player_row["formerTeam"].split(',')  # Former teams (previous)
      debut_team = player_row["debutTeam"]  # Debut team
      
      # Insert into teamPlayer table for present teams
      for team_name in current_teams:
        cur.execute("SELECT ID FROM team WHERE fullName = %s", (team_name,))
        team_result = cur.fetchone()
        if team_result:
            team_id = team_result[0]

            # Insert for the present team
            debute = False
            present = True
            previous = False
            if team_name == debut_team:  # If this is the debut team
                debute = True

            # Insert into the teamPlayer table
            cur.execute("""
                INSERT INTO teamPlayer (team_ID, player_ID, debute, present, previous)
                VALUES (%s, %s, %s, %s, %s)
            """, (team_id, player_id, debute, present, previous))

      for team_name in former_teams:
        cur.execute("SELECT ID FROM team WHERE fullName = %s", (team_name,))
        team_result = cur.fetchone()
        if team_result:
          team_id = team_result[0]

          # Insert for the previous team
          debute = False
          present = False
          previous = True

          # Insert into the teamPlayer table
          cur.execute("""
              INSERT INTO teamPlayer (team_ID, player_ID, debute, present, previous)
              VALUES (%s, %s, %s, %s, %s)
          """, (team_id, player_id, debute, present, previous))


if __name__ == "__main__":
  #Create database connection, use the password that was sent to you by email
  try:
    conn = pymysql.connect(host='localhost', port=3306, user='root', passwd='', db='test')
    cur = conn.cursor()

    insert_teams()
    insert_coaches()
    insert_players()
    insert_teamPlayers()

    #Close the database connection
    cur.close()
    conn.commit()
    conn.close()
  except BaseException as error:
    print("Något gick fel i databasen.", error)
