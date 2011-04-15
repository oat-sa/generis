ALTER TABLE `statements`
  DROP `UsersOthers`,
  DROP `UsersMyGroup`,
  DROP `UsersMe`,
  DROP `Anonymous`,
  DROP `SubscribersOthers`,
  DROP `SubscribersMyGroup`,
  DROP `authorgroup`;

 UPDATE statements SET mask = 'rwrwr[g1,g2][g1]' WHERE mask = '2222220012' 