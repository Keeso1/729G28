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
  if dictitem != None:
    if "{" in dictitem:
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
    else:
      dictitem = dictitem.strip()
      placeholders = "%s, %s"
      itemquery = f"INSERT INTO {tablename} {value_tuple} VALUES ({placeholders})"
      itemval = (id, dictitem)
      cur.execute(itemquery, itemval)
      #Uppdaterad för redovisning

          
def checkIfNoneAndStrip(row):
  for key, value in row.items():
    row[key] = row[key].strip()
    if value == "NULL":
      row[key] = None

def get_team_coaches():
  with open('AmericanFootballTeam.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    teamCoaches = {}

    for row in csvreader:
      checkIfNoneAndStrip(row)
      if row["coach"] != None:
        teamCoaches[int(row["coach"])] = int(row["id"])

  
    return teamCoaches



def insert_coaches():
  with open('AmericanFootballCoach.csv', 'r') as csvfile:
    csvreader = csv.DictReader(csvfile, delimiter = ',')

    for row in csvreader:
      checkIfNoneAndStrip(row)

      row["ID"] = int(row["ID"])


      coaches = get_team_coaches()

      if row["ID"] in coaches:
        team_ID = coaches[row["ID"]]
      else:
        team_ID = None


      row["birthDate"] = fix_date(row["birthDate"])
      
      insert_query= "INSERT INTO coach (ID, fullName, info, birthDate, birthPlace, team_ID) VALUES (%s, %s, %s, %s, %s, %s)"
      val = (row["ID"], row["name"], row["information"], row["birthDate"], row["birthPlace"], team_ID) #TODO

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

def split_multivalued_TP(dictitem):
  if dictitem != None and "{" in dictitem:
    itemlist = dictitem.replace(dictitem[0], "")
    itemlist = itemlist.replace(dictitem[-1], "")
    itemlist = itemlist.split("|")
    return itemlist
  else:
    itemlist = []
    itemlist.append(dictitem)
    return itemlist

def resolve_conflicts(nestedlist):
  merged_lsts = []
  updated_nestedlist = []
  processed_indices = set()

  for i, lst1 in enumerate(nestedlist):
    if i in processed_indices:
      continue

    for j, lst2 in enumerate(nestedlist):
      merged = False
      if i != j and (lst1[1] == lst2[1]):
        merged_booleans = [a or b for a,b in zip(lst1[2:], lst2[2:])]
        merged_list = lst1[:2] + merged_booleans
        merged_lsts.append(merged_list)
        processed_indices.add(i)
        processed_indices.add(j)
        merged = True
        break

    if not merged:
      updated_nestedlist.append(lst1)

  updated_nestedlist.extend(merged_lsts)
  return updated_nestedlist


def make_new_ID():
  cur.execute("select max(ID) from team")
  max_ID = cur.fetchone()
  return int(max_ID[0]) + 1

def list_of_team_names():
  cur.execute("select fullName from team")
  team_names = cur.fetchall()
  team_namelist = []
  for tup in team_names:
    team_namelist.append(tup[0])
  return team_namelist

def check_if_exist_make_new_team(team_name):
  if team_name != None:
    exist = False
    for name in list_of_team_names():
      if team_name == name:
        exist = True
        break
    if not exist:
      
      if team_name != "Free agent":
        new_id = make_new_ID()
        val = (new_id, team_name, None, None)
        cur.execute("INSERT INTO team (ID, fullName, ownerName, creationDate) VALUES (%s, %s, %s, %s)", val)
      
  return team_name
  #Nytt inför redovisning

        

def insert_teamPlayers():
  """Uppdaterad inför redovisning"""
  with open('AmericanFootballPlayer.csv', 'r') as csvPlayers:
    players = csv.DictReader(csvPlayers, delimiter = ',')

    for player_row in players:
      checkIfNoneAndStrip(player_row) #Har redan gjorts men ändå
      player_id = int(player_row["ID"])

      current_teams = split_multivalued_TP(player_row["team"])
      former_teams = split_multivalued_TP(player_row["formerTeam"])
      debut_team = check_if_exist_make_new_team(player_row["debutTeam"])
      
      finalSave = []
      for team_name in current_teams:
        check_if_exist_make_new_team(team_name)
        debute = False
        if team_name == debut_team:
          debute = True
        try:
          cur.execute("SELECT ID FROM team WHERE fullName = %s", (team_name))
          team_result = cur.fetchone()
          team_id = team_result[0]

          present = True
          previous = False

          save = [player_id, team_id, debute, present, previous]
          finalSave.append(save)
        except:
          continue
          
        
      for team_name in former_teams:
        check_if_exist_make_new_team(team_name)
        debute = False
        if team_name == debut_team:
            debute = True
        try:
          cur.execute("SELECT ID FROM team WHERE fullName = %s", (team_name))
          team_result = cur.fetchone()
          team_id = team_result[0]

          present = False
          previous = True
          
          save = [player_id, team_id, debute, present, previous]
          finalSave.append(save)
        except:
          continue

      finalSave = resolve_conflicts(finalSave)

      for lst in finalSave:
        cur.execute("INSERT INTO teamPlayer (team_ID, player_ID, debute, present, previous) VALUES (%s, %s, %s, %s, %s)", (lst[1], lst[0], lst[2], lst[3], lst[4]))

if __name__ == "__main__":
  #Create database connection, use the password that was sent to you by email
  try:
    conn = pymysql.connect(host='mariadb.edu.liu.se', port=3306, user='klabe908', passwd='klabe90879b6', db='klabe908')
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
