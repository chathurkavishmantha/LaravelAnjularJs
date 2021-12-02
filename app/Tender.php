<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property tinyint $category_id
 * @property int $createdBy
 * @property string $name
 * @property string $code
 * @property date $dateAdded
 * @property datetime $dateClosing
 * @property tinyint $status
 * @property string $tenderFile
 * @property int $id
 */

class Tender extends Model
{
    //
    
    protected $dates = ['dateAdded' ,'dateClosing'];
    
    protected $fillable = ['name','code','category_id','status' ];
    
    protected  $appends = ['statusText','tempURL'];
	
	/**
	@var Updates won't cause log entries
	*/
	public static $noLog = false;

    public static $useTempURL=false;

    public function category(){
        return $this->belongsTo(Category::class , 'category_id' , 'id');
    }
    
    public function postedBy(){
        return $this->belongsTo(User::class , 'createdBy' , 'id');
    }
    
    public function getStatusTextAttribute(){
        
        switch($this->status):
            case 1 :
                return 'Closed';
            default:
                return 'Open';
        endswitch;
        
    }
    
    public function deleteFile($oldFile = null){
        @unlink( storage_path('tenders/' . ( $oldFile ?? $this->tenderFile)));
    }
    
    public function items(){
        return $this->hasMany(TenderItem::class , 'tender_id' , 'id');
    }
    
    public function getTempURLAttribute(){
        if(self::$useTempURL):
            return url()->temporarySignedRoute('api.download-tender' , now()->addMinutes(30) , $this->id);
            else:
            return '';
        endif;
    }
    
}
