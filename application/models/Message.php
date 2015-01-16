<?php

/** 
 * @Entity
 * @Table(name="message")
 * 
 * Sample Doctrine Entity
 */
class Message
{
	/** 
	 * @Id 
	 * @Column(type="integer") 
	 * @GeneratedValue
	 */
	private $id;

	/** 
	 * @Column(length=140) 
	 */
	private $text;
	
	/** 
	 * @Column(type="datetime", name="posted_at") 
	 */
	private $postedAt;

	public function __construct()
	{
		$this->postedAt = new DateTime();
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getPostedAt()
	{
		return $this->postedAt;
	}

	/**
	 * @param mixed $postedAt
	 */
	public function setPostedAt($postedAt)
	{
		$this->postedAt = $postedAt;
	}
}