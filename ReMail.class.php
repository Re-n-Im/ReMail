<?
class ReMail
{
	// Private variables
	
	private $sMessage = '';
	private $aMailHeaders = array
	(
		'MIME-Version' => '1.0',
		'Content-Type' => 'text/html; charset=utf-8',
		'Subject' => '',
		'From' => '',
		'To' => '',
		'Cc' => '',
		'Bcc' => '',
		'Reply-To' => ''
	);
	private $aEMails = array
	(
		'To' => array( ),
		'Cc' => array( ),
		'Bcc' => array( ),
		'Reply-To' => array( )
	);
	private $sBoundary = '';
	private $aAttachments = array( );
	
	// The constructor
	
	public function __construct( )
	{
		$this->sBoundary = 'ReMail-'.md5( mt_rand( 0 , time( ) ) );
	}
	
	// Private methods
	
	private function checkEMails( )
	{
		$tempArr = array( );
		
		foreach( $this->aEMails as $em )
		{
			$tempArr = array_merge( $tempArr , $em );
		}
		
		return !!count( $tempArr );
	}
	
	private function setContentType( $ct )
	{
		if( $ct == 'mixed' )
			$this->setHeader( 'Content-Type' , 'multipart/mixed; Boundary="'.$this->getBoundary( ).'"' );
		else
			$this->setHeader( 'Content-Type' , 'text/html; charset=UTF-8' );
	}
	
	private function getMessage( )
	{
		return $this->sMessage;
	}
	
	private function getSubject( )
	{
		return $this->aMailHeaders[ 'Subject' ];
	}
	
	private function getHeaders( )
	{
		$tempArr = array( );
		
		$this->normalizeEMails( );
		
		foreach( $this->aMailHeaders as $kh => $h )
		{
			if( $h )
				$tempArr[] = $kh.': '.$h;
		}
		
		return implode( "\n" , $tempArr );
	}
	
	private function getTo( )
	{
		$this->normalizeEMails( );
		return $this->aMailHeaders[ 'To' ];
	}
	
	private function normalizeEMails( )
	{
		foreach( $this->aEMails as $kem => $em )
		{
			array_filter( $this->aEMails[ $kem ] );
			$this->aMailHeaders[ $kem ] = implode( ', ' , $this->aEMails[ $kem ] );
		}
	}
	
	private function normalizeFrom( )
	{
		if( $this->aMailHeaders[ 'From' ] == '' )
		{
			$this->aMailHeaders[ 'From' ] = 'Undefined sender';
		}
	}
	
	private function normalizeSubject( )
	{
		if( $this->aMailHeaders[ 'Subject' ] == '' )
		{
			$this->aMailHeaders[ 'Subject' ] = '[no subject]';
		}
	}
	
	private function getBoundary( )
	{
		return $this->sBoundary;
	}
	
	private function setHeader( $hdr , $val = '' )
	{
		if( is_string( $hdr ) && $val != '' )
		{
			if( array_key_exists( $hdr , $this->aMailHeaders ) )
			{
				$this->aMailHeaders[ $hdr ] = $val;
			}
		}
		elseif( is_array( $hdr ) )
		{
			foreach( $hdr as $k => $h )
			{
				if( array_key_exists( $k , $this->aMailHeaders ) )
				{
					$this->aMailHeaders[ $k ] = $h;
				}
			}
		}
	}
	
	private function addMails( $mailType , $emails )
	{
		if( is_string( $emails ) )
		{
			$this->aEMails[ $mailType ] = array_merge( $this->aEMails[ $mailType ] , preg_split( '/\s*,\s*/ui' , trim( $emails ) ) );
		}
		elseif( is_array( $emails ) )
		{
			$this->aEMails[ $mailType ] = array_merge( $this->aEMails[ $mailType ] , $emails );
		}
	}
	
	// Public methods
	
	public function addAttachment( $atta , $type = 'attachment' )
	{
		if( is_string( $atta ) )
		{
			$atta = array( $atta );
		}
		
		$type = in_array( $type , array( 'inline' , 'attachment' ) ) ? $type : 'attachment';
		
		foreach( $atta as $at )
		{
			$fileData = file_get_contents( $at );
			
			if( $fileData )
			{
				$finfo = new finfo( FILEINFO_MIME_TYPE );
				$mime = $finfo->buffer( $fileData );
				
				$fname = basename( $at );
				
				$imageSrc = ''.count( $this->aAttachments ).'__='.preg_replace( '/\s+/mui' , '' , $fname ).'@local';
				
				$this->aAttachments[] =
				'Content-type: '.$mime.'; name="'.$fname.'"'.PHP_EOL
				.'Content-Disposition: '.$type.'; filename="'.$fname.'"'.PHP_EOL
				.'Content-ID: <'.$imageSrc.'>'.PHP_EOL
				.'Content-transfer-encoding: base64'.PHP_EOL
				.''.PHP_EOL
				.chunk_split( base64_encode( $fileData ) );
			}
		}
	}
	
	public function setMessage( $txt )
	{
		if( !preg_match( '/<img [^>]+>/mui' , $txt ) )
		{
			$this->sMessage = $txt;
		}
		else
		{
			while( preg_match( '/(<img[^>]+src=")(http[^">]+)("[^>]+>)/mui' , $txt , $mm ) )
			{
				$fname = basename( $mm[ 2 ] );
				$imageSrc = 'cid:'.count( $this->aAttachments ).'__='.preg_replace( '/\s+/mui' , '' , $fname ).'@local';
				$this->addAttachment( $mm[ 2 ] , 'inline' );
				
				$txt = str_replace( $mm[ 0 ] , '<br />'.$mm[ 1 ].$imageSrc.$mm[ 3 ].'<br />' , $txt );
			}
			
			$this->sMessage = $txt;
		}
	}
	
	public function addBcc( $bcc )
	{
		$this->addMails( 'Bcc' , $bcc );
	}
	
	public function addCc( $cc )
	{
		$this->addMails( 'Cc' , $cc );
	}
	
	public function addTo( $to )
	{
		$this->addMails( 'To' , $to );
	}
	
	public function addReplyTo( $rt )
	{
		$this->addMails( 'Reply-To' , $rt );
	}
	
	public function setFrom( $from )
	{
		$this->setHeader( 'From' , $from );
	}
	
	public function setSubject( $subj )
	{
		$this->setHeader( 'Subject' , $subj );
	}
	
	public function send( )
	{
		$bIsMix = !!count( $this->aAttachments );
		
		$this->normalizeEMails( );
		$this->normalizeFrom( );
		$this->normalizeSubject( );
		
		if( $this->checkEMails( ) )
		{
			if( $bIsMix )
			{
				$this->setContentType( 'mixed' );
				
				$this->sMessage =
				'--'.$this->getBoundary( ).PHP_EOL
				.'Content-type: text/html; charset=utf-8'.PHP_EOL
				.'Content-Disposition: inline'.PHP_EOL
				.'Content-transfer-encoding: base64'.PHP_EOL
				.''.PHP_EOL
				.chunk_split( base64_encode( $this->getMessage( ) ) ).PHP_EOL
				.'--'.$this->getBoundary( ).PHP_EOL
				.implode( "\n--".$this->getBoundary( )."\n" , $this->aAttachments ).PHP_EOL
				.'--'.$this->getBoundary( ).'--';
			}
			
			$sendMail = mail
			(
				$this->getTo( ),
				$this->getSubject( ),
				$this->getMessage( ),
				$this->getHeaders( )
			);
			
			return $sendMail;
		}
		else
			return 'no-valid-emails';
	}
}
?>
