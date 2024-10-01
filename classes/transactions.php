<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class Transactions extends Eloquent
{
   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
   protected $fillable = [
       'checkout_id', 'amount', 'meter','phone', 'created_at', 'updated_at'
   ];
   
 }