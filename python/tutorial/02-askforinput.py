# --------------------------------------------
# Asking for input
# See http://www.tropo.com for more info
# --------------------------------------------

answer()

result = ask("Hi. For sales, press 1. For support, press 2.", {'choices':"1,2"})

if (result.name == 'choice'):
  if (result.value == "1"): say("Sales is not available right now")
  if (result.value == "2"): say("Support is currently on the other line.")

hangup()
