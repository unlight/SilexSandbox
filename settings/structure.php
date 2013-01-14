<?php

$user = R::load('user', -1);
$user->id = -1;
$user->name = Column::String();
$user->password = Column::String();
$user->email = Column::String();
$user->hash_method = Column::String();
$user->gender = '';
$user->provider = Column::String();
$user->provider_uid = Column::String();
$user->date_inserted = Column::DateTime();
$user->date_updated = Column::DateTime();

R::store($user);